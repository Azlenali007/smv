/**
 * ApexPulse SMM Platform Frontend Assets
 * Pure JavaScript (.js) + Alpine.js
 * Zero TypeScript / Zero React
 */

import Alpine from 'alpinejs';

// Register global Alpine store for live preview & UI state
Alpine.store('smmApp', {
  brandName: 'ApexPulse',
  baseCurrency: 'INR',
  displayCurrency: 'INR',
  currencySymbol: '₹',
  exchangeRates: {
    INR: 1.0,
    USD: 86.50,
    EUR: 94.20,
    GBP: 110.80,
  },

  setCurrency(curr) {
    this.displayCurrency = curr;
    if (curr === 'USD') this.currencySymbol = '$';
    else if (curr === 'EUR') this.currencySymbol = '€';
    else if (curr === 'GBP') this.currencySymbol = '£';
    else this.currencySymbol = '₹';
  },

  convertInrToDisplay(amountInInr) {
    const rate = this.exchangeRates[this.displayCurrency] || 1.0;
    if (this.displayCurrency === 'INR') {
      return parseFloat(amountInInr).toFixed(2);
    }
    return (parseFloat(amountInInr) / rate).toFixed(2);
  },

  format(amountInInr) {
    return this.currencySymbol + this.convertInrToDisplay(amountInInr);
  }
});

window.Alpine = Alpine;
Alpine.start();
