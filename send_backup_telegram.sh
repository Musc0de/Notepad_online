#!/bin/bash

# Direktori yang akan di-backup
BACKUP_DIR="/home/user/public_html"

# Lokasi penyimpanan backup ZIP
BACKUP_FILE="/home/user/backup_$(date +\%Y\%m\%d).zip"

# Membuat file ZIP
zip -r $BACKUP_FILE $BACKUP_DIR

# Token API dan Chat ID dari Telegram
BOT_TOKEN="7412767854:AAFs3OBlh__PKlL1dRljzX1UsffRL0q6aJ4"
CHAT_ID="1373922140"  # Ganti dengan Chat ID Telegram Anda

# Mengirim file ke Telegram
curl -F chat_id=$CHAT_ID -F document=@"$BACKUP_FILE" "https://api.telegram.org/bot$BOT_TOKEN/sendDocument"
