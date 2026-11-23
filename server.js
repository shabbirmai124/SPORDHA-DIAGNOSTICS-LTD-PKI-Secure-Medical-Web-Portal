// Md Shabbir Mia
import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';
import os from 'node:os';
import { loadNodeRuntime, createNodeFsMountHandler } from '@php-wasm/node';
import { PHP, PHPRequestHandler, setPhpIniEntries } from '@php-wasm/universal';

const PORT = Number(process.env.PORT || 3000);
const HOST = '0.0.0.0';
const ROOT_DIR = process.cwd();
const SESSION_DIR = path.join(os.tmpdir(), 'spordha_php_sessions');

if (!fs.existsSync(SESSION_DIR)) {
  fs.mkdirSync(SESSION_DIR, { recursive: true });
}

const MIME_TYPES = {
  '.html': 'text/html; charset=utf-8',
  '.css': 'text/css; charset=utf-8',
  '.js': 'application/javascript; charset=utf-8',
  '.json': 'application/json; charset=utf-8',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.gif': 'image/gif',
  '.svg': 'image/svg+xml',
  '.ico': 'image/x-icon',
  '.webp': 'image/webp',
  '.pdf': 'application/pdf',
  '.woff': 'font/woff',
  '.woff2': 'font/woff2',
  '.ttf': 'font/ttf',
  '.txt': 'text/plain; charset=utf-8',
};

const BLOCKED_PREFIXES = ['/config/', '/database/', '/includes/', '/node_modules/', '/.'];

let nextProcessId = 1;

async function createPhpInstance() {
  const runtime = await loadNodeRuntime('8.3', {
    emscriptenOptions: {
      processId: nextProcessId++,
    },
  });
  const php = new PHP(runtime);
  php.mkdir('/www');
  php.mount('/www', createNodeFsMountHandler(ROOT_DIR));
  if (!php.fileExists('/tmp/sessions')) {
    php.mkdir('/tmp/sessions');
  }
  php.mount('/tmp/sessions', createNodeFsMountHandler(SESSION_DIR));
  await setPhpIniEntries(php, {
    'session.save_path': '/tmp/sessions',
    display_errors: '0',
    'date.timezone': 'Asia/Dhaka',
  });
  return php;
}

const requestHandler = new PHPRequestHandler({
  documentRoot: '/www',
  absoluteUrl: `http://localhost:${PORT}`,
  cookieStore: false,
  phpFactory: async () => createPhpInstance(),
  maxPhpInstances: 4,
});

function readRequestBody(req) {
  return new Promise((resolve, reject) => {
    const chunks = [];
    req.on('data', (chunk) => chunks.push(chunk));
    req.on('end', () => resolve(Buffer.concat(chunks)));
    req.on('error', reject);
  });
}

function isBlockedPath(pathname) {
  const lower = pathname.toLowerCase();
  if (lower.endsWith('.db') || lower.endsWith('.sql') || lower === '/server.js' || lower === '/package.json' || lower === '/package-lock.json') {
    return true;
  }
  return BLOCKED_PREFIXES.some((prefix) => lower.startsWith(prefix));
}

function serveStaticFile(pathname, res) {
  let decoded;
  try {
    decoded = decodeURIComponent(pathname);
  } catch {
    return false;
  }
  const normalized = path.posix.normalize(decoded).replace(/^(\.\.(\/|\\|$))+/, '');
  if (isBlockedPath(normalized)) {
    res.writeHead(403, { 'Content-Type': 'text/plain; charset=utf-8' });
    res.end('403 Forbidden');
    return true;
  }
  const filePath = path.join(ROOT_DIR, normalized);
  const resolved = path.resolve(filePath);
  if (!resolved.startsWith(path.resolve(ROOT_DIR))) {
    return false;
  }
  if (!fs.existsSync(resolved) || !fs.statSync(resolved).isFile()) {
    return false;
  }
  const ext = path.extname(resolved).toLowerCase();
  if (ext === '.php') {
    return false;
  }
  const contentType = MIME_TYPES[ext] || 'application/octet-stream';
  res.writeHead(200, {
    'Content-Type': contentType,
    'X-Content-Type-Options': 'nosniff',
  });
  fs.createReadStream(resolved).pipe(res);
  return true;
}

async function handleRequest(req, res) {
  try {
    const rawUrl = req.url || '/';
    const parsed = new URL(rawUrl, `http://${req.headers.host || `localhost:${PORT}`}`);
    let pathname = parsed.pathname;

    if (pathname === '/') {
      pathname = '/index.php';
    } else if (!path.extname(pathname)) {
      const candidate = path.join(ROOT_DIR, `${pathname}.php`);
      if (fs.existsSync(candidate) && fs.statSync(candidate).isFile()) {
        pathname = `${pathname}.php`;
      }
    }

    if (isBlockedPath(pathname)) {
      res.writeHead(403, { 'Content-Type': 'text/plain; charset=utf-8' });
      res.end('403 Forbidden');
      return;
    }

    if (serveStaticFile(pathname, res)) {
      return;
    }

    const bodyBuffer = req.method !== 'GET' && req.method !== 'HEAD' ? await readRequestBody(req) : undefined;
    const headers = {};
    for (const [key, value] of Object.entries(req.headers)) {
      if (value !== undefined) {
        headers[key.toLowerCase()] = Array.isArray(value) ? value.join('; ') : value;
      }
    }

    const phpResponse = await requestHandler.request({
      url: `${pathname}${parsed.search}`,
      method: req.method || 'GET',
      headers,
      body: bodyBuffer && bodyBuffer.length > 0 ? new Uint8Array(bodyBuffer) : undefined,
    });

    const outHeaders = {
      'Strict-Transport-Security': 'max-age=31536000; includeSubDomains; preload',
      'X-Content-Type-Options': 'nosniff',
      'Referrer-Policy': 'strict-origin-when-cross-origin',
    };

    const isHttps =
      String(headers['x-forwarded-proto'] || '').split(',')[0].trim().toLowerCase() === 'https' ||
      String(headers['origin'] || headers['referer'] || '').toLowerCase().startsWith('https://');

    for (const [key, values] of Object.entries(phpResponse.headers || {})) {
      const lowerKey = key.toLowerCase();
      if (lowerKey === 'transfer-encoding' || lowerKey === 'content-length') {
        continue;
      }
      if (lowerKey === 'set-cookie') {
        const rawList = Array.isArray(values) ? values : [values];
        outHeaders['Set-Cookie'] = rawList.map((cookieStr) => {
          let updated = String(cookieStr).replace(/;\s*SameSite=[^;]*/gi, '');
          if (!/;\s*HttpOnly/i.test(updated)) {
            updated += '; HttpOnly';
          }
          if (isHttps) {
            if (!/;\s*Secure/i.test(updated)) {
              updated += '; Secure';
            }
            updated += '; SameSite=None';
            if (!/;\s*Partitioned/i.test(updated)) {
              updated += '; Partitioned';
            }
          } else {
            updated += '; SameSite=Lax';
          }
          return updated;
        });
      } else {
        outHeaders[key] = Array.isArray(values) ? values.join(', ') : values;
      }
    }

    if (!outHeaders['content-type'] && !outHeaders['Content-Type']) {
      outHeaders['Content-Type'] = 'text/html; charset=utf-8';
    }

    const statusCode = phpResponse.httpStatusCode || 200;
    const payload = Buffer.from(phpResponse.bytes);
    res.writeHead(statusCode, outHeaders);
    res.end(payload);
  } catch (err) {
    res.writeHead(500, { 'Content-Type': 'text/plain; charset=utf-8' });
    res.end('Server error: ' + (err && err.message ? err.message : String(err)));
  }
}

await requestHandler.getPrimaryPhp();

const server = http.createServer((req, res) => {
  void handleRequest(req, res);
});

server.listen(PORT, HOST, () => {
  console.log(`Spordha Diagnostics Ltd. PHP Server running on http://${HOST}:${PORT}`);
});
