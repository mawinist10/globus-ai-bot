
# Globus AI Room Bot (Final Version)

✅ Telegram bot for visualizing furniture in user's room using AI (RoomGPT)

### Features:
- Manual language selection (RU / EN) with "Back" option
- Upload 1–5 furniture photos + 1 room photo
- Detects existing furniture in photo and removes it via smart prompts
- Allows user to pick lighting, decor, and interior style
- PDF with logo, QR, links, and generated visualization
- Inline buttons: Contact manager, Tour, Regenerate, Leave contact
- Contact and session log to Google Sheets
- 3x/hour generation limit per user

### Deploy steps:
1. Upload this code to your GitHub repo
2. Connect to Render and deploy as Web Service (Docker)
3. Set these environment variables:
    - TELEGRAM_BOT_TOKEN
    - REPLICATE_API_TOKEN
    - GOOGLE_SHEET_SCRIPT_URL

4. Done! The bot is ready to visualize and generate leads.
