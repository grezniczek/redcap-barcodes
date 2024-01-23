var DE_RUB_Barcodes;
(function () {
    const barcodes = {
        qr: setupQRCode,
        dm: setupDatamatrix,
        code39: setupCode39
    }
    DE_RUB_Barcodes = barcodes;
    
    function setupQRCode(tag) {
        const id = 'barcodes-qr-' + tag.field;
        const $tb = $('input[name="' + tag.field + '"]');
        const text = '' + $tb.val();
        // Hide text box
        $tb.hide();
        // Hide "View equation" link
        $tb.parent().parent().find('.viewEq').hide();
        const qrSpan = document.createElement('span');
        qrSpan.style.display = 'inline-block';
        qrSpan.setAttribute('id', id);
        $tb.before(qrSpan);
        const qrcode = new QRCode(qrSpan, {
            text: text,
            width: tag.size,
            height: tag.size,
            colorDark : "#000000",
            colorLight : "transparent",
            correctLevel : QRCode.CorrectLevel.H
        });
        if (tag.link) {
            $(qrSpan).wrap(`<a href="${text}" target="_blank"></a>`);
        }

        $('form#form').on('change', function() { 
            const text = '' + $tb.val();
            qrcode.clear();
            qrcode.makeCode(text);
            if (tag.link) {
                $(qrSpan).parent('a').attr('href', text);
            }
        });
    }


    function setupDatamatrix(tag) {
        const id = 'barcodes-dm-' + tag.field;
        const $tb = $('input[name="' + tag.field + '"]');
        const text = '' + $tb.val();
        const padding = 0;
        // Hide text box
        $tb.hide();
        // Hide "View equation" link
        $tb.parent().parent().find('.viewEq').hide();
        const dmSpan = document.createElement('span');
        dmSpan.style.display = 'inline-block';
        dmSpan.setAttribute('id', id);
        $tb.before(dmSpan);
        const dmSvg = DATAMatrix({
            msg : text,
            dim : tag.size,
            rct : 0,
            pad : padding,
            pal : ["#000000", "transparent"],
            vrb : 0
        });
        dmSpan.appendChild(dmSvg);
        if (tag.link) {
            $(dmSpan).wrap(`<a href="${text}" target="_blank"></a>`);
        }

        $('form#form').on('change', function() { 
            const text = '' + $tb.val();
            const dmSvg = DATAMatrix({
                msg : text,
                dim : tag.size,
                rct : 0,
                pad : padding,
                pal : ["#000000", "transparent"],
                vrb : 0
            });
            dmSpan.innerHTML = '';
            dmSpan.appendChild(dmSvg);
            if (tag.link) {
                $(dmSpan).parent('a').attr('href', text);
            }
        });
    }

    const code39Classes = {
        true: {
            false: 'barcodes-code-39-extended',
            true: 'barcodes-code-39-extended-text'
        },
        false: {
            false: 'barcodes-code-39',
            true: 'barcodes-code-39-text'
        }
    }

    function setupCode39(tag) {
        const id = 'barcodes-c39-' + tag.field;
        const $tb = $('input[name="' + tag.field + '"]');
        const text = '' + $tb.val();
        // Hide text box
        $tb.hide();
        // Hide "View equation" link
        $tb.parent().parent().find('.viewEq').hide();
        const span = document.createElement('span');
        span.style.display = 'inline-block';
        span.classList.add(tag.text ? 'barcodes-code-39-text' : 'barcodes-code-39');
        span.style.fontSize = tag.size + 'px';
        span.setAttribute('id', id);
        span.textContent = '*'+text+'*';
        $tb.before(span);
        $('form#form').on('change', function() { 
            const text = '' + $tb.val();
            span.textContent = '*'+text+'*';
        });
    }
})();