var tableToExcel = (function () {
    // Define your style class template.
    var style = "<style>.head { background-color: #0089ca; color: #fff;} .head th {padding:10px;} .body td { border: 1px solid; padding:6px;} .right {text-align: right}  .center {text-align: center}</style>";
    var uri = 'data:application/vnd.ms-excel;base64,'
        , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->' + style + '</head><body><table>{table}</table></body></html>'
        , base64 = function (s) {
            return window.btoa(unescape(encodeURIComponent(s)))
        }
        , format = function (s, c) {
            return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
        }
    return function (table, name) {
        if (!table.nodeType) table = document.getElementById(table)
        var ctx = { worksheet: 'Task Recap' || 'Worksheet', table: table.innerHTML }
        var csvContent = uri + base64(format(template, ctx))
        // window.location.href = uri + base64(format(template, ctx))

        /* create a hidden <a> DOM node and set its download attribute */
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", name + ".xls");
        document.body.appendChild(link);
         /* download the data file named "Stock_Price_Report.csv" */
        link.click();
    }
})()

function exportWord(name, targetId){
    var style = "<style> #"+targetId+" { font-family: Arial; } .text-primary { color:#0089ca } </style>";
    var header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' "+
         "xmlns:w='urn:schemas-microsoft-com:office:word' "+
         "xmlns='http://www.w3.org/TR/REC-html40'>"+
         "<head><meta charset='utf-8'><title>Export HTML to Word Document with JavaScript</title>"+ style +"</head><body>";
    var footer = "</body></html>";
    var sourceHTML = header+document.getElementById(targetId).innerHTML+footer;
    
    var source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
    var fileDownload = document.createElement("a");
    document.body.appendChild(fileDownload);
    fileDownload.href = source;
    fileDownload.download = name + '.doc';
    fileDownload.click();
    document.body.removeChild(fileDownload);
 }