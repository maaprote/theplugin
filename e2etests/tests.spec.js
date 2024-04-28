/**
 * End to end tests.
 * 
 * I won't write so much tests here. Just a few to show my knowledge.
 */
const { test, expect } = require('@playwright/test');

// You must replace the URL with your local environment URL.
const url = 'http://theplugin.ddev.site/';

test('The validation from search by email input is functional', async ({ page }) => {
	await page.goto(url);
    await page.fill('.rtp-newsletter-entries__input', 'test');
    await page.click('.rtp-newsletter-entries__form-submit');
    await page.waitForSelector('.rtp-newsletter-entries__error');
    const error = await page.innerText('.rtp-newsletter-entries__error');
    expect(error).toBe('Invalid email.');
});