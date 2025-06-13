const fs = require('fs');
const path = require('path');
const archiver = require('archiver');

console.log('🔨 Building StorageFlow Free Version...');

const pluginDir = path.resolve(__dirname, '..');
const distDir = path.join(pluginDir, 'dist', 'free');
const outputDir = path.join(distDir, 'storageflow-free');

// Clean and create directories
if (fs.existsSync(distDir)) {
    fs.rmSync(distDir, { recursive: true });
}
fs.mkdirSync(distDir, { recursive: true });
fs.mkdirSync(outputDir);

// Copy essential files
const filesToCopy = [
    'storageflow.php',
    'readme.txt',
    'core'
];

filesToCopy.forEach(file => {
    const src = path.join(pluginDir, file);
    const dest = path.join(outputDir, file);
    
    if (fs.existsSync(src)) {
        if (fs.statSync(src).isDirectory()) {
            fs.cpSync(src, dest, { recursive: true });
        } else {
            fs.copyFileSync(src, dest);
        }
        console.log(`✅ Copied ${file}`);
    } else {
        console.log(`⚠️ Missing ${file}`);
    }
});

// Create ZIP
const zipPath = path.join(distDir, 'storageflow-free.zip');
const output = fs.createWriteStream(zipPath);
const archive = archiver('zip', { zlib: { level: 9 } });

archive.pipe(output);
archive.directory(outputDir, 'storageflow-free');
archive.finalize();

output.on('close', () => {
    console.log(`✅ ZIP created: ${zipPath} (${(archive.pointer() / 1024 / 1024).toFixed(2)} MB)`);
    console.log('🚀 StorageFlow free version ready!');
});