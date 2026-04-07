import { test, expect } from '@playwright/test';
import { haveUser, cleanupVarDir } from './helpers/user';

test.beforeEach(() => {
    haveUser('admin', 'password', ['user', 'admin']);
});

test.afterEach(() => {
    cleanupVarDir();
});

test('loginAndLogout', async ({ page, context }) => {
    await page.goto('/');

    const cookiesBefore = await context.cookies();
    expect(cookiesBefore.find(c => c.name === 'blue-auth')).toBeUndefined();

    await expect(page.getByRole('link', { name: 'Log in' })).toBeVisible();

    await page.goto('/login');
    await page.locator('form[name="form"] [name="form[username]"]').fill('admin');
    await page.locator('form[name="form"] [name="form[password]"]').fill('password');
    await page.locator('form[name="form"] button[type="submit"]').click();

    await page.waitForURL('/');
    await expect(page.getByText('You are currently authenticated as')).toBeVisible();

    const cookiesAfter = await context.cookies();
    expect(cookiesAfter.find(c => c.name === 'blue-auth')).toBeDefined();

    await page.goto('/logout');

    await expect(page.getByRole('link', { name: 'Log in' })).toBeVisible();

    const cookiesFinal = await context.cookies();
    expect(cookiesFinal.find(c => c.name === 'blue-auth')).toBeUndefined();
});
