// Google Apps Script для логирования
function doPost(e) {
  var sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName('Leads');
  var data = JSON.parse(e.postData.contents);
  sheet.appendRow([new Date(), data.chat_id, data.language, data.event, data.detail]);
  return ContentService.createTextOutput('OK');
}