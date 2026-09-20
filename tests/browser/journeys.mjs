import { chromium, expect as baseExpect } from '@playwright/test';
import fs from 'node:fs/promises';
const expect = baseExpect.configure({ timeout: 20000 });

await fs.mkdir('test-results', { recursive: true });
const browser = await chromium.launch({ channel: 'chrome', headless: true });
const page = await browser.newPage({ viewport: { width: 1440, height: 1000 }, acceptDownloads: true });
page.setDefaultTimeout(30000);
const errors = [];
page.on('pageerror', e => errors.push(e.message));
const visit = async path => {
    const response = await page.goto(`http://localhost:8000${path}`, { waitUntil: 'networkidle', timeout: 60000 });
    expect(response.status(), path).toBe(200);
};
try {
    if (!process.argv.includes('--admin-only')) {
    await visit('/');
    const announcement = page.locator('[data-announcement-popup]');
    if (await announcement.isVisible()) {
        await announcement.getByRole('button', { name: 'Close', exact: true }).click();
    }
    await page.locator('footer').scrollIntoViewIfNeeded();
    await expect.poll(() => page.locator('img[alt="WhatsApp QR"]').evaluate(image => image.naturalWidth)).toBeGreaterThan(0);
    await page.evaluate(() => window.scrollTo(0, 0));
    await page.screenshot({ path: 'test-results/home-desktop.png', fullPage: true });
    await page.setViewportSize({ width: 390, height: 844 });
    await page.evaluate(() => window.scrollTo(0, 0));
    await page.screenshot({ path: 'test-results/home-mobile-viewport.png' });
    await page.screenshot({ path: 'test-results/home-mobile.png', fullPage: true });
    await page.getByRole('button', { name: 'Open navigation', exact: true }).click();
    await expect(page.locator('#mobile-menu')).toBeVisible();
    await page.locator('#mobile-menu').getByRole('link', { name: 'Academics', exact: true }).click();
    await expect(page.getByRole('heading', { name: 'Academics', exact: true })).toBeVisible();
    await visit('/id');
    await expect(page.getByRole('heading', { name: 'Kabar terbaru JTS' })).toBeVisible();
    await visit('/zh');
    await expect(page.getByRole('heading', { name: '校園最新消息' })).toBeVisible();
    console.log('PASS: homepage, QR image, mobile navigation, Indonesian, Traditional Chinese');

    await page.setViewportSize({ width: 1440, height: 1000 });
    await visit('/news');
    await expect(page.locator('main article')).toHaveCount(10);
    await page.getByRole('searchbox').fill('no-such-topic-xyz');
    await expect(page.getByRole('heading', { name: 'No results found' })).toBeVisible();
    await page.getByRole('button', { name: 'Clear filters' }).click();
    await expect(page.locator('main article')).toHaveCount(10);
    await page.getByLabel('Department', { exact: true }).selectOption({ label: 'Academic Office' });
    await expect(page.locator('main article')).toHaveCount(7);
    await page.screenshot({ path: 'test-results/news-desktop.png', fullPage: true });
    await visit('/id/resources/calendar');
    await expect(page.locator('tbody tr')).toHaveCount(4);
    await page.getByLabel('Jenjang sekolah', { exact: true }).selectOption('sd');
    await expect(page.locator('tbody tr')).toHaveCount(1);
    await expect(page).toHaveURL(/school_level=sd/);
    await expect(page.getByRole('columnheader', { name: 'Tanggal unggah' })).toBeVisible();
    const downloadHref = await page.locator('tbody a[title="Unduh"]').getAttribute('href');
    const downloadResponse = await page.request.get(new URL(downloadHref, page.url()).href);
    expect(downloadResponse.status()).toBe(200);
    expect(downloadResponse.headers()['content-disposition']).toMatch(/attachment;.*\.pdf/i);
    await page.setViewportSize({ width: 390, height: 844 });
    await page.screenshot({ path: 'test-results/resources-mobile.png', fullPage: true });
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    console.log('PASS: Livewire search, department and school-level dropdown filters, locale persistence, PDF download');
    }

    await page.setViewportSize({ width: 1440, height: 1000 });
    await visit('/admin/login');
    await page.getByLabel('Email address').fill('admin@jts.local');
    await page.locator('input[type="password"]').fill('JtsLocal2026!');
    await page.getByRole('button', { name: 'Sign in', exact: true }).click();
    await page.waitForURL('**/admin', { timeout: 60000 });
    await page.waitForLoadState('networkidle');
    await expect(page.locator('.fi-topbar')).toHaveCount(0);
    await expect(page.locator('.fi-sidebar-header-logo-ctn img')).toBeVisible();
    await expect(page.locator('.fi-sidebar-footer .fi-user-menu')).toBeVisible();
    const profileTrigger = page.locator('.fi-sidebar-footer .fi-user-menu-trigger');
    await profileTrigger.click();
    const profileMenu = page.locator('.fi-dropdown-panel:visible');
    await expect(profileMenu).toBeVisible();
    const profileTriggerBox = await profileTrigger.boundingBox();
    const profileMenuBox = await profileMenu.boundingBox();
    expect(profileMenuBox.x).toBeGreaterThanOrEqual(profileTriggerBox.x + profileTriggerBox.width);
    await page.keyboard.press('Escape');
    await page.screenshot({ path: 'test-results/admin-desktop.png', fullPage: true });
    await page.setViewportSize({ width: 390, height: 844 });
    await page.getByRole('button', { name: 'Expand sidebar' }).click();
    await expect(page.locator('.fi-sidebar-header-logo-ctn img')).toBeVisible();
    await expect(page.locator('.fi-sidebar-footer .fi-user-menu')).toBeVisible();
    await page.screenshot({ path: 'test-results/admin-mobile.png', fullPage: true, animations: 'disabled' });
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    await expect(page.getByRole('link', { name: 'Pages', exact: true })).toHaveCount(0);
    await page.setViewportSize({ width: 1440, height: 1000 });
    await visit('/about');
    await expect(page.getByRole('heading', { name: 'About Jakarta Taipei School' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Our school community' })).toBeVisible();
    await page.setViewportSize({ width: 390, height: 844 });
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    await visit('/id/admissions');
    await expect(page.getByRole('heading', { name: 'Pendaftaran' })).toBeVisible();
    await expect(page.getByRole('link', { name: 'program Mandarin' })).toHaveAttribute('href', '/id/mandarin-program');
    console.log('PASS: Pages removed from admin and static localized pages render');
    expect(errors).toEqual([]);
    console.log('PASS: no JavaScript errors');
} finally {
    await page.screenshot({ path: 'test-results/journey-last-state.png', fullPage: true });
    await browser.close();
}
