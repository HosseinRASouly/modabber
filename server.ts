import express from 'express';
import { spawn } from 'child_process';
import http from 'http';

const app = express();
app.disable('x-powered-by');
const PORT = 3000;
const PHP_PORT = 8080;

// Ensure PHP background server is running
let phpProcess: any = null;

function startPhpServer() {
  phpProcess = spawn('php', ['-S', `127.0.0.1:${PHP_PORT}`, '-t', 'public', 'public/router.php'], {
    stdio: 'inherit',
  });

  phpProcess.on('error', (err: any) => {
    console.error('PHP server error:', err);
  });
}

// Start PHP process
startPhpServer();

// Reverse proxy middleware to PHP
app.use((req, res) => {
  const options: http.RequestOptions = {
    hostname: '127.0.0.1',
    port: PHP_PORT,
    path: req.url,
    method: req.method,
    headers: {
      ...req.headers,
      host: req.headers.host || `localhost:${PORT}`,
      'x-forwarded-for': req.ip,
      'x-forwarded-proto': req.protocol,
    },
  };

  const phpReq = http.request(options, (phpRes) => {
    res.writeHead(phpRes.statusCode || 200, phpRes.headers);
    phpRes.pipe(res);
  });

  phpReq.on('error', (err) => {
    console.error('Proxy error to PHP:', err);
    res.status(502).send('Error connecting to Modabber PHP backend.');
  });

  req.pipe(phpReq);
});

app.listen(PORT, '0.0.0.0', () => {
  console.log(`Modabber Server running on http://0.0.0.0:${PORT}`);
});
