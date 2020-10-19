
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
    if (card.match(/^5[1-5]\d/g)) {
      if  (jQuery('#card-type').text() !== 'MasterCard' ) {
        jQuery( "#card-type" ).text( "MasterCard" );
      }
    } else if (card.match(/^4\d/g)) {
      if  (jQuery('#card-type').text() !== 'Visa' ) {
        jQuery( "#card-number" ).text( "Visa" );
      }
    } else if (card.match(/^3[47]\d/g)) {
      if  (jQuery('#card-type').text() !== 'American Express' ) {
        jQuery( "#card-type" ).text( "American Express" );
      }
    } else if (card.match(/^6011\d/g)) {
      if  (jQuery('#card-type').text() !== 'Discover' ) {
        jQuery( "#card-type" ).text( "Discover" );
      }
    } else {
        jQuery( "#card-type" ).text( "None" );
    }
    var matches = card.match(/\d{4,16}/g);
    this.value = creditCardFormat(this.value, matches);
    if ( this.value.length == 19) {
      var luhn_checksum = valid_credit_card(this.value);
      if( luhn_checksum == false ) {
        if (jQuery( 'form.woocommerce-checkout' ).hasClass('error-message') == false || jQuery('.error-message').text() !== 'Please enter a valid card number' ) {
            jQuery('#card-number_field').append("<p class='error-message'>Please enter a valid card number</p>");
          
        } 
      }
      
    } else {
      jQuery('error-message').remove();
    } 
    
  });



  jQuery( 'form.woocommerce-checkout' ).on('keyup', '#card-expiry', function() {
    var inputChar = String.fromCharCode(this.keyCode);
    var code = this.keyCode;
    var allowedKeys = [8];
    if (allowedKeys.indexOf(code) !== -1) {
        return;
    }   
    var card = this.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    var matches = card.match(/\d{2,4}/g)
    
    var match = matches && matches[0] || ''
    var parts = []
    for (i=0, len=match.length; i<len; i+=2) {
      parts.push(match.substring(i, i+2))
    }
    if (parts.length) {
      this.value = parts.join('/')
    } else {
      this.value = this.value
    } 
    var minMonth = new Date().getMonth() + 1;  
    var minYear = new Date().getFullYear().toString().substr(-2);
    var month = parseInt(parts[0], 10);
    var year = parseInt(parts[1], 10);

    if (year < minYear || (year === minYear && month < minMonth)){
      jQuery('#card-expiry').append("<p class='error-message'>Please enter a valid expiry date</p>");

    };  
  });
  
  jQuery( 'form.woocommerce-checkout' ).on('keyup', '#card-ccv', function() {
    var ccv = this.value.replace(/[^\d\/]|^[\/]*$/g, '');
    var matches = ccv.match(/\d{3}/g);
    var match = matches && matches[0] || ''
    var parts = []
    for (i=0, len=match.length; i<len; i+=1) {
      parts.push(match.substring(i, i+1))
    }
    if (parts.length) {
      this.value = parts.join('')
    } else {
      this.value = ccv
    }
});
});