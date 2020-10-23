
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

  jQuery( 'form.woocommerce-checkout' ).on('keyup', '#card-number', function() {
    var card = this.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    var image_path = credit_cards.image_path;
    if (card.match(/^5[1-5]\d/g)) {
      if  (jQuery('#card-type').text() !== 'MasterCard' ) {
        jQuery('input#card-number').after("<img src='" + image_path + "master-card.png'/>");
        jQuery( "#card-type" ).text( "MasterCard" );
      }
    } else if (card.match(/^4\d/g)) {
      if  (jQuery('#card-type').text() !== 'Visa' ) {
        jQuery( "#card-type" ).text( "Visa" );
        jQuery('input#card-number').after("<img src='" + image_path + "visa-card.png'/>");
      }
    } else if (card.match(/^3[47]\d/g)) {
      if  (jQuery('#card-type').text() !== 'American Express' ) {
        jQuery( "#card-type" ).text( "American Express" );
        jQuery('input#card-number').after("<img src='" + image_path + "american_express.png'/>");
      }
    } else if (card.match(/^6011\d/g)) {
      if  (jQuery('#card-type').text() !== 'Discover' ) {
        jQuery( "#card-type" ).text( "Discover" );
        jQuery('input#card-number').after("<img src='" + image_path + "discover-card.png'/>");
      }
    } else {
        jQuery( "#card-type" ).text( "None" );
        jQuery('input#card-number').next("img").remove();
    }
    var matches = card.match(/\d{4,16}/g);
  
    this.value = creditCardFormat(this.value, matches);
    
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
      parts.push(match.substring(i, i+2));
    }
    if (parts.length) {
      this.value = parts.join('/')
    } else {
      this.value = this.value
    } 
    jQuery('#expiry-month').val(parts[0]);
    jQuery('#expiry-year').val(parts[1]);
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