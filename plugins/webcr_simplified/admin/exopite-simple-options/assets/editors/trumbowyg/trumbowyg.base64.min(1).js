!function(e){"use strict";var a=function(){return"undefined"!=typeof FileReader},r=function(e){return/^data:image\/[a-z]?/i.test(e)};e.extend(!0,e.trumbowyg,{langs:{en:{base64:"Image as base64",file:"File",errFileReaderNotSupported:"FileReader is not supported by your browser.",errInvalidImage:"Invalid image file."},fr:{base64:"Image en base64",file:"Fichier"},cs:{base64:"Vložit obrázek",file:"Soubor"},zh_cn:{base64:"图片（Base64编码）",file:"文件"},nl:{base64:"Afbeelding inline",file:"Bestand",errFileReaderNotSupported:"Uw browser ondersteunt deze functionaliteit niet.",errInvalidImage:"De gekozen afbeelding is ongeldig."},ru:{base64:"Изображение как код в base64",file:"Файл",errFileReaderNotSupported:"FileReader не поддерживается вашим браузером.",errInvalidImage:"Недопустимый файл изображения."},ja:{base64:"画像 (Base64形式)",file:"ファイル",errFileReaderNotSupported:"あなたのブラウザーはFileReaderをサポートしていません",errInvalidImage:"画像形式が正しくありません"},tr:{base64:"Base64 olarak resim",file:"Dosya",errFileReaderNotSupported:"FileReader tarayıcınız tarafından desteklenmiyor.",errInvalidImage:"Geçersiz resim dosyası."}},plugins:{base64:{shouldInit:a,init:function(i){var t={isSupported:a,fn:function(){i.saveRange();var a,t=i.openModalInsert(i.lang.base64,{file:{type:"file",required:!0,attributes:{accept:"image/*"}},alt:{label:"description",value:i.getRangeText()}},function(n){var l=new FileReader;l.onloadend=function(a){r(a.target.result)?(i.execCmd("insertImage",l.result),e(['img[src="',l.result,'"]:not([alt])'].join(""),i.$box).attr("alt",n.alt),i.closeModal()):i.addErrorOnModalField(e("input[type=file]",t),i.lang.errInvalidImage)},l.readAsDataURL(a)});e("input[type=file]").on("change",function(e){a=e.target.files[0]})}};i.addBtnDef("base64",t)}}}})}(jQuery);