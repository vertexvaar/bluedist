import { test, expect } from '@playwright/test';
import { haveUser, cleanupVarDir } from './helpers/user';

test.beforeEach(() => {
    haveUser('admin', 'password', ['user', 'admin']);
});

test.afterEach(() => {
    cleanupVarDir();
});

async function loginAs(page: any, username: string, password: string) {
    await page.goto('/login');
    await page.locator('#login [name="username"]').fill(username);
    await page.locator('#login [name="password"]').fill(password);
    await page.locator('#login button[type="submit"]').click();
    await page.waitForURL('/');
}

test('createInitialFruitsTest', async ({ page }) => {
    await page.goto('/');
    await expect(page.getByText('Welcome to VerteXVaaR.BlueSprints')).toBeVisible();

    await page.getByRole('link', { name: 'follow me' }).click();
    await expect(page.getByText('OH WAIT! There is no Fruit yet.')).toBeVisible();

    await page.getByRole('button', { name: 'Create a bunch of fruits' }).click();
    await expect(page.getByRole('link', { name: 'Apple' })).toBeVisible();

    await loginAs(page, 'admin', 'password');

    await page.goto('/listFruits');
    await page.getByRole('link', { name: 'Apple' }).click();
    await expect(page.getByText('Change a Apple')).toBeVisible();

    await page.getByLabel('Color').fill('green-red');
    await page.getByRole('button', { name: 'Submit' }).click();

    await page.waitForURL('/listFruits');
    await expect(page.getByText('green-red')).toBeVisible();
});

test('deleteAllFruits', async ({ page }) => {
    await page.goto('/');

    await page.getByRole('link', { name: 'follow me' }).click();
    await expect(page.getByText('OH WAIT! There is no Fruit yet.')).toBeVisible();

    await page.getByRole('button', { name: 'Create a bunch of fruits' }).click();
    await expect(page.getByText('Apple')).toBeVisible();

    await loginAs(page, 'admin', 'password');

    await page.goto('/listFruits');
    await page.getByRole('button', { name: 'Delete all fruits' }).click();

    await page.waitForURL('/listFruits');
    await expect(page.getByText('OH WAIT! There is no Fruit yet.')).toBeVisible();
});
