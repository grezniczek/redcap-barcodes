# Barcodes

A REDCap external module that transforms text fields to barcodes on data entry forms and surveys.

## Installation

- Clone this repo into `<redcap-root>/modules/redcap_barcodes_<version-number>`, or
- Obtain this module from the Consortium [REDCap Repo](https://redcap.vanderbilt.edu/consortium/modules/index.php) via the Control Center.
- Go to _Control Center > Technical / Developer Tools > External Modules_ and enable **Barcodes**.

## Requirements

- REDCAP 14.0.0 or newer

## Configuration

- There are no module-specific system or project configuration options.
- All action is controlled by the .... action tags ;)

## Action Tags

`@BARCODE="Type"`

where `Type` is one of:
- `Code 39` 
- `Code 128`
- `EAN-8`
- `EAN-13`, optionally add `+2` or `+5` for the 2-/5-digit addon
- `UPC-A`, optionally add `+2` or `+5` for the 2-/5-digit addon
- `UPC-E`, optionally add `+2` or `+5` for the 2-/5-digit addon
- `QR` (QR Code; 2D) 
- `DM` (Datamatrix; 2D)

Add ` Text` (with a leading space) to any of the 1D barcodes to display the encoded string below the barcode.

Separated by commas, optionally add the size (integer values only) and whether the barcode should act as a hyperlink (`L``; QR and DM only).

EAN/UPC barcodes must be valid codes. Optionally, the check digit can be replaces with a `?`. Check [here](https://graphicore.github.io/librebarcode/documentation/ean13#ean13-encoder) for details.

Examples: 
- `@BARCODE="Code 39 Text, 40` = Code 39 with text, text size 40px
- `@BARCODE="QR, 100, L` = A somewhat reduced size QR code (default size = 128) that is a hyperlink
- `@BARCODE="EAN-13` = A standard EAN-13 barcode. A valid example value would be 001234567890? or 0012345678905 (5 is the correct check digit)

Apply this action tag to a field of type **Text Box** (no validation) only. If you want to have it editable on the page, use the `@BARCODE` on field with `@CALCTEXT` or `@CALCDATE`. If you need the barcode piped, use field embedding instead.


## Use case

- Whenever you want to _display_ a barcode on a data entry forms or survey pages.

## Notes & Limitations

This will **not** work for PDFs, emails, etc., but for form display only, as all the magic happens in the client's browser!

Have a look at the [QRCode EM](https://github.com/grezniczek/redcap-qrcode) if you need to include QR codes in other places than data entry forms and surveys.

## Changelog

Version | Description
------- | ------------------
v1.0.0  | Initial release.
