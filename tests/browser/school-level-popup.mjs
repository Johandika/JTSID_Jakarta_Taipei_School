import { chromium, expect as baseExpect } from '@playwright/test';
import fs from 'node:fs/promises';

const expect = baseExpect.configure({ timeout: 15000 });
const browser = await chromium.launch({ channel: 'chrome', headless: true });
const context = await browser.newContext({ viewport: { width: 1440, height: 1000 } });
const page = await context.newPage();
const errors = [];
context.on('page', tab => tab.on('pageerror', error => errors.push(error.message)));
page.on('pageerror', error => errors.push(error.message));
page.setDefaultTimeout(30000);
const title = `Browser popup check ${Date.now()}`;
let editUrl;
const visit = async (tab, path) => {
    const response = await tab.goto(`http://localhost:8000${path}`, { waitUntil: 'networkidle', timeout: 60000 });
    expect(response.status()).toBe(200);
};

try {
    await fs.mkdir('test-results', { recursive: true });
    await visit(page, '/id/resources/calendar');
    await page.getByLabel('Jenjang sekolah', { exact: true }).selectOption('sd');
    await expect(page.locator('tbody tr')).toHaveCount(1);
    await visit(page, '/news');
    await expect(page.getByLabel('School level', { exact: true })).toHaveValue('all');
    await visit(page, '/id/resources/calendar');
    await expect(page.locator('tbody tr')).toHaveCount(4);

    await visit(page, '/admin/login');
    await page.getByLabel('Email address').fill('admin@jts.local');
    await page.locator('input[type="password"]').fill('JtsLocal2026!');
    await page.getByRole('button', { name: 'Sign in', exact: true }).click();
    await page.waitForURL('**/admin');
    await visit(page, '/admin/announcement-popups/create');
    await page.getByRole('textbox', { name: /^Title/ }).fill(title);
    await page.getByRole('textbox', { name: 'Message', exact: true }).fill('Selamat kepada siswa JTS atas prestasinya.');
    await page.getByRole('switch').click();
    await page.locator('input[type="file"]').setInputFiles('public/images/highlight.webp');
    await expect(page.locator('.filepond--item')).toHaveCount(1);
    await expect(page.locator('.filepond--item[data-filepond-item-state="processing-complete"]')).toHaveCount(1);
    await page.getByRole('button', { name: 'Create', exact: true }).click();
    await page.waitForURL(/\/admin\/announcement-popups\/\d+\/edit/);
    editUrl = page.url();
    await page.screenshot({ path: 'test-results/popup-admin.png', fullPage: true });

    const visitor = await context.newPage();
    await visitor.clock.install();
    await visit(visitor, '/id');
    const dialog = visitor.getByRole('dialog');
    await expect(dialog).toBeVisible();
    await expect(dialog.getByRole('heading', { name: title })).toBeVisible();
    await expect.poll(() => visitor.evaluate(() => Object.entries(sessionStorage).some(([key, value]) => key.startsWith('jts-announcement-') && value === 'initial-shown'))).toBe(true);
    await expect.poll(() => dialog.locator('img').evaluate(img => img.naturalWidth)).toBeGreaterThan(0);
    await visitor.screenshot({ path: 'test-results/popup-desktop.png' });
    await visitor.setViewportSize({ width: 390, height: 844 });
    await visitor.screenshot({ path: 'test-results/popup-mobile.png' });
    expect(await visitor.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    await dialog.getByRole('button', { name: 'Tutup', exact: true }).click();
    await expect(dialog).not.toBeVisible();
    await visitor.clock.fastForward(30_000);
    await visitor.mouse.move(20, 20);
    await visitor.clock.fastForward(89_000);
    await expect(dialog).not.toBeVisible();
    await visitor.clock.fastForward(1_000);
    await expect(dialog).toBeVisible();
    await expect.poll(() => visitor.evaluate(() => Object.entries(sessionStorage).some(([key, value]) => key.startsWith('jts-announcement-') && value === 'complete'))).toBe(true);
    await dialog.getByRole('button', { name: 'Tutup', exact: true }).click();
    await visit(visitor, '/id/news');
    await visitor.clock.fastForward(90_000);
    await expect(visitor.getByRole('dialog')).not.toBeVisible();
    await visitor.reload({ waitUntil: 'networkidle' });
    await visitor.clock.fastForward(90_000);
    await expect(visitor.getByRole('dialog')).not.toBeVisible();

    const freshTab = await context.newPage();
    await freshTab.clock.install();
    await visit(freshTab, '/id');
    await expect(freshTab.getByRole('dialog')).toBeVisible();
    await freshTab.keyboard.press('Escape');
    await expect(freshTab.getByRole('dialog')).not.toBeVisible();
    await freshTab.clock.fastForward(90_000);
    await expect(freshTab.getByRole('dialog')).toBeVisible();
    await freshTab.keyboard.press('Escape');
    await expect(freshTab.getByRole('dialog')).not.toBeVisible();
    expect(await freshTab.evaluate(() => document.body.style.overflow)).toBe('');
    expect(errors).toEqual([]);
    console.log('PASS: immediate popup, one 90-second idle reminder, activity reset, desktop/mobile popup, close, Escape, and two-show session limit.');
} catch (error) {
    await page.screenshot({ path: 'test-results/popup-browser-failure.png', fullPage: true });
    console.log('Form state:', (await page.locator('body').innerText()).slice(-3500));
    console.log('File upload state:', await page.locator('.filepond--item').evaluateAll(items => items.map(item => ({ state: item.dataset.filepondItemState, text: item.innerText }))));
    throw error;
} finally {
    if (editUrl) {
        await page.goto(editUrl, { waitUntil: 'networkidle' });
        await page.getByRole('button', { name: 'Delete', exact: true }).click();
        await page.getByRole('alertdialog').getByRole('button', { name: 'Delete', exact: true }).click();
        await page.waitForURL('**/admin/announcement-popups');
    }
    await browser.close();
}
