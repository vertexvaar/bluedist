import { test, expect } from '@playwright/test';
import { cleanupVarDir } from './helpers/user';

test.afterEach(() => {
    cleanupVarDir();
});

test('rootPageTest', async ({ page }) => {
    await page.goto('/');
    await expect(page.getByText('Welcome to VerteXVaaR.BlueSprints')).toBeVisible();
});
