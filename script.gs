// Google Apps Script to log data from bot to Google Sheet
function doPost(e) {
  var sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
  var data = JSON.parse(e.postData.contents);
  sheet.appendRow([new Date(), data.chat_id, data.language, data.action, data.detail]);
  return ContentService.createTextOutput("ok");
}