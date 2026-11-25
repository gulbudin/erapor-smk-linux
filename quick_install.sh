#!/bin/bash
# ==============================================================================
# One-Line Quick Installer untuk e-Rapor SMK Kurikulum Merdeka (Linux Server Edition)
# Cukup jalankan: curl -sSL https://raw.githubusercontent.com/gulbudin/erapor-smk-linux/main/quick_install.sh | bash
# ==============================================================================

set -e

if [ "$EUID" -ne 0 ]; then
    echo "[ERROR] Harap jalankan dengan akses root (sudo bash atau login sebagai root)"
    exit 1
fi

echo "=============================================================================="
echo "  MEMULAI INSTALASI CEPAT (ONE-CLICK) e-Rapor SMK Kurikulum Merdeka (Linux Server Edition)"
echo "=============================================================================="

export DEBIAN_FRONTEND=noninteractive
echo "[1/3] Memperbarui paket sistem & menginstal Git & Curl..."
apt-get update -y
apt-get install -y git curl ca-certificates

TARGET_DIR="/var/www/eraporsmk"
echo "[2/3] Mengunduh berkas e-Rapor ke $TARGET_DIR..."
if [ -d "$TARGET_DIR/.git" ]; then
    echo "Direktori $TARGET_DIR sudah ada, mengambil pembaruan..."
    cd "$TARGET_DIR" && git pull origin main
else
    rm -rf "$TARGET_DIR"
    git clone https://github.com/gulbudin/erapor-smk-linux.git "$TARGET_DIR"
fi

echo "[3/3] Menjalankan instalasi sistem..."
cd "$TARGET_DIR"
chmod +x install_linux.sh
bash install_linux.sh
