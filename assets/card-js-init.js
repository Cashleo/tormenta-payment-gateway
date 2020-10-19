
jQuery(document).ready(function(){
  
  function creditCardFormat(value, matches) {
    
    var match = matches && matches[0] || ''
    var parts = []
    for (i=0, len=match.length; i<len; i+=4) {
      parts.push(match.substring(i, i+4))
    }
    if (parts.length) {
      return parts.join(' ')
    } else {
      return value
    }
  }

  function valid_credit_card(value) {
    // Accept only digits, dashes or spaces
    if (/[^0-9-\s]+/.test(value)) return false;
  
    // The Luhn Algorithm. It's so pretty.
    let nCheck = 0, bEven = false;
    value = value.replace(/\D/g, "");
  
    for (var n = value.length - 1; n >= 0; n--) {
      var cDigit = value.charAt(n),
          nDigit = parseInt(cDigit, 10);
  
      if (bEven && (nDigit *= 2) > 9) nDigit -= 9;
  
      nCheck += nDigit;
      bEven = !bEven;
    }
  
    return (nCheck % 10) == 0;
  }
  jQuery( 'form.woocommerce-checkout' ).on('keyup', '#card-number', function() {
    var card = this.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    var first = card.substring(0, 4)
    if (first.match(/^5[1-5]\d/g)) {
      if  (jQuery('#card-type').text() !== 'MasterCard' ) {
        jQuery( "#card-type" ).text( "MasterCard" );
      }
    } else if (first.match(/^4\d/g)) {
      if  (jQuery('#card-type').text() !== 'Visa' ) {
        jQuery( "#card-number" ).text( "Visa" );
      }
    } else if (first.match(/^3[47]\d/g)) {
      if  (jQuery('#card-type').text() !== 'American Express' ) {
        jQuery( "#card-type" ).text( "American Express" );
      }
    } else if (first.match(/^6011\d/g)) {
      if  (jQuery('#card-type').text() !== 'Discover' ) {
        jQuery( "#card-type" ).text( "Discover" );
      }
    } else {
        jQuery( "#card-type" ).text( "None" );
    }
    var matches = card.match(/\d{4,16}/g);
    this.value = creditCardFormat(this.value, matches);
    valid_credit_card(this.value);
    
  });

function cgDateValidate(whatDate) {
  var currVal = whatDate;

  if (currVal === '') {
      return false;
  }

  var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
  var dtArray = currVal.match(rxDatePattern);

  if (dtArray == null) {
      return false;
  }

  // Check for dd/mm/yyyy format
  var dtDay = dtArray[1],
      dtMonth= dtArray[3],
      dtYear = dtArray[5];

  if (dtMonth < 1 || dtMonth > 12) {
      return false;
  } else if (dtDay < 1 || dtDay> 31) {
      return false;
  } else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31) {
      return false;
  } else if (dtMonth == 2) {
      var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
      if (dtDay> 29 || (dtDay ==29 && !isleap)) {
          return false;
      }
  }

  return true;
}

  jQuery( 'form.woocommerce-checkout' ).on('keyup', '#card-expiry', function() {
    var inputChar = String.fromCharCode(this.keyCode);
    var code = this.keyCode;
    var allowedKeys = [8];
    if (allowedKeys.indexOf(code) !== -1) {
        return;
    }

    var expiry = this.value.replace(
        /^([1-9]\/|[2-9])$/g, '0$1/' // 3 > 03/
    ).replace(
        /^(0[1-9]|1[0-2])$/g, '$1/' // 11 > 11/
    ).replace(
        /^([0-1])([3-9])$/g, '0$1/$2' // 13 > 01/3
    ).replace(
        /^(0?[1-9]|1[0-2])([0-9]{2})$/g, '$1/$2' // 141 > 01/41
    ).replace(
        /^([0]+)\/|[0]+$/g, '0' // 0/ > 0 and 00 > 0
    ).replace(
        /[^\d\/]|^[\/]*$/g, '' // To allow only digits and `/`
    ).replace(
        /\/\//g, '/' // Prevent entering more than 1 `/`
    );
    if (expiry.length > 5) {
      return
    }
    this.value = expiry;
  });
  
  jQuery( 'form.woocommerce-checkout' ).on('keyup', '#card-ccv', function() {
    var ccv = this.value.replace(/[^\d\/]|^[\/]*$/g, '');
    if (ccv.val().length < 3) {
        cgToggleError($(this), 'invalid');
    }
});
});