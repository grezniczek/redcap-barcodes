var DE_RUB_Barcodes;
(function () {
    const barcodes = {
        qr: setupQRCode
    }
    DE_RUB_Barcodes = barcodes;
    
    function setupQRCode(tag) {
        const id = 'barcodes-' + tag.field;
        const $tb = $('input[name="' + tag.field + '"]');
        const tb = $tb.get(0);
        const text = '' + $tb.val();
        // Hide text box
        $tb.hide();
        // Hide "View equation" link
        $tb.parent().parent().find('.viewEq').hide();
        const qrDiv = document.createElement('div');
        qrDiv.setAttribute('id', id);
        $tb.before(qrDiv);
        const qrcode = new QRCode(qrDiv, {
            text: text,
            width: tag.size,
            height: tag.size,
            colorDark : "#000000",
            colorLight : "transparent",
            correctLevel : QRCode.CorrectLevel.H
        });
        if (tag.link) {
            $(qrDiv).wrap(`<a href="${text}" target="_blank"></a>`);
        }

        $('form#form').on('change', function() { 
            const text = '' + $tb.val();
            qrcode.clear();
            qrcode.makeCode(text);
            if (tag.link) {
                $(qrDiv).parent('a').attr('href', text);
            }
        });
    }
})();