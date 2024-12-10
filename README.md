# Barcodes

A REDCap external module that transforms text fields to barcodes on data entry forms and surveys.

## Installation

- Clone this repo into `<redcap-root>/modules/redcap_barcodes_<version-number>`, or
- Obtain this module from the Consortium [REDCap Repo](https://redcap.vanderbilt.edu/consortium/modules/index.php) via the Control Center.
- Go to _Control Center > Technical / Developer Tools > External Modules_ and enable **Barcodes**.

## Requirements

- REDCap 14.0.0 or newer

## Configuration

- There are no module-specific system or project configuration options.
- All action is controlled by ... the action tag ;)

## Use case

Whenever you want to _display_ a barcode on a data entry forms or survey pages, this is the right module for the job ;)


## Action Tags

`@BARCODES="Type"`

where `Type` is one of:
- `Code 39` 
- `Code 39 Extended` 
- `Code 128`
- `EAN/UPC` (wide range of EAN and UPC barcodes, including addons)
- `QR` (QR Code; 2D) 
- `DM` (Datamatrix; 2D)

Add ` Text` (with a leading space) to any of the 1D barcodes to display the encoded string below the barcode. For EAN/UPC barcodes, text will always be displayed.

Separated by commas, optionally add the size (integer values only)and whether the barcode should act as a hyperlink (`L`; QR and DM only). The default sizes are 48 for Code 39 and Code 128, and 128 for EAN/UPC and the 2D barcodes, respectively.

Apply this action tag to a field of type **Text Box** only. The barcode will be shown instead of the input element. If you want to have the field editable on the page, use the `@BARCODES` action tag on a different field that "pipes" in the value with `@CALCTEXT` or `@CALCDATE`. If you need the barcode piped to some place, use the field embedding feature.

EAN/UPC barcodes must be valid codes. Optionally, the check digit can be replaces with a `?`. Check [here](https://graphicore.github.io/librebarcode/documentation/ean13#ean13-encoder) for details, and the examples below.

For the intricacies of Code 39 vs. Code 39 Extended, see [this article](https://graphicore.github.io/librebarcode/documentation/code39.html). For more info on Code 128, see [this article](https://graphicore.github.io/librebarcode/documentation/code128.html).

Examples: 
- `@BARCODES="Code 39 Text, 40"` = Code 39 with text, text size 40px
- `@BARCODES="QR, 100, L"` = A somewhat reduced size QR code (default size = 128) that is a hyperlink
- `@BARCODES="EAN/UPC"` = EAN-8, EAN-13, UPC-A, UPC-E barcodes with otional addons. The check digit can be replaces with `?`. 

Here is a list of valid EAN/UPC example values (D = a digit):

Symbol | Pattern | Example
------ | ------- | -------
EAN-13 | DDDDDDDDDDDD? | 001234567890? 
EAN-13 | DDDDDDDDDDDDD | 0012345678905 (5 is the correct check digit)
EAN-8  | DDDDDDD? | 1234567?
EAN-8  | DDDDDDDD | 12345670 (0 is the correct check digit)
UPC-A  | DDDDDDDDDDD? | 01234567890? 
UPC-A  | DDDDDDDDDDDD | 012345678905 (5 is the correct check digit)
UPC-E (short) | xDDDDDD? | x123455?
UPC-E (short) | xDDDDDDD | x1234558 (8 is the correct check digit)
UPC-E (long) | XDDDDDDDDDDD? | X09840000075?
UPC-E (long) | XDDDDDDDDDDDD | X098400000751 (1 is the correct check digit)
2-digit add-on | -DD | -34
5-digit add-on | -DDDDD | -87613
EAN-13+2 | DDDDDDDDDDDD?DD | 001234567890?12 
EAN-13+2 | DDDDDDDDDDDDDDD | 001234567890512 
EAN-13+5 | DDDDDDDDDDDD?DDDDD | 001234567890?12345 
EAN-13+5 | DDDDDDDDDDDDDDDDDD | 001234567890512345 

## Notes & Limitations

This will **not** work for PDFs, emails, etc., but for form display only, as all the magic happens in the client's browser!

Have a look at the [QRCode EM](https://github.com/grezniczek/redcap-qrcode) if you need to include QR codes in other places than data entry forms and surveys.

## Changelog

Version | Description
------- | ------------------
v1.0.2  | Add missing fonts.
v1.0.1  | Fix misspelled action tag in the README.<br>Bump Framework version to 14.
v1.0.0  | Initial release.
