import { test, expect, Page } from '@playwright/test';
import { haveUser, cleanupVarDir } from './helpers/user';

test.beforeEach(() => {
    haveUser('admin', 'password', ['user', 'admin']);
});

test.afterEach(() => {
    cleanupVarDir();
});

async function loginAsAdmin(page: Page): Promise<void> {
    await page.goto('/login');
    await page.locator('form[name="form"] [name="form[username]"]').fill('admin');
    await page.locator('form[name="form"] [name="form[password]"]').fill('password');
    await page.locator('form[name="form"] button[type="submit"]').click();
    await page.waitForURL('/');
}

test('admin fruits list is accessible', async ({ page }) => {
    await loginAsAdmin(page);
    await page.goto('/admin/fruits');

    await expect(page).toHaveTitle(/BlueAdmin/);
    await expect(page.getByRole('link', { name: 'Create Fruit' })).toBeVisible();
});

test('admin can create a fruit', async ({ page }) => {
    await loginAsAdmin(page);
    await page.goto('/admin/fruits/create');

    await expect(page.getByLabel('Name')).toBeVisible();
    await expect(page.getByLabel('Color')).toBeVisible();

    await page.getByLabel('Name').fill('Mango');
    await page.getByLabel('Color').fill('Yellow');
    await page.getByRole('button', { name: 'Save Fruit' }).click();

    // redirects to show page after creation
    await expect(page.getByText('Fruit Details')).toBeVisible();
    await expect(page.locator('input[value="Mango"]')).toBeVisible();
    await expect(page.locator('input[value="Yellow"]')).toBeVisible();
});

test('admin show page displays fields as disabled', async ({ page }) => {
    await loginAsAdmin(page);

    // Create a fruit first
    await page.goto('/admin/fruits/create');
    await page.getByLabel('Name').fill('Strawberry');
    await page.getByLabel('Color').fill('Red');
    await page.getByRole('button', { name: 'Save Fruit' }).click();

    // Should be on show page now
    await expect(page.getByText('Fruit Details')).toBeVisible();

    // Fields must be disabled (context disabled = no <form>, inputs have disabled attr)
    const nameInput = page.locator('input[value="Strawberry"]');
    const colorInput = page.locator('input[value="Red"]');
    await expect(nameInput).toBeVisible();
    await expect(colorInput).toBeVisible();
    await expect(nameInput).toBeDisabled();
    await expect(colorInput).toBeDisabled();

    // No submit button on show page
    await expect(page.getByRole('button', { name: 'Save Fruit' })).not.toBeVisible();

    // Edit button must be present
    await expect(page.getByRole('link', { name: 'Edit Fruit' })).toBeVisible();
});

test('admin edit page pre-fills form with entity values', async ({ page }) => {
    await loginAsAdmin(page);

    // Create a fruit first
    await page.goto('/admin/fruits/create');
    await page.getByLabel('Name').fill('Banana');
    await page.getByLabel('Color').fill('Yellow');
    await page.getByRole('button', { name: 'Save Fruit' }).click();

    // Navigate to edit via the Edit button on show page
    await page.getByRole('link', { name: 'Edit Fruit' }).click();

    await expect(page).toHaveURL(/\/edit$/);
    await expect(page.getByLabel('Name')).toHaveValue('Banana');
    await expect(page.getByLabel('Color')).toHaveValue('Yellow');

    // Fields must be editable
    await expect(page.getByLabel('Name')).not.toBeDisabled();
    await expect(page.getByLabel('Color')).not.toBeDisabled();
});

test('admin can update a fruit', async ({ page }) => {
    await loginAsAdmin(page);

    // Create
    await page.goto('/admin/fruits/create');
    await page.getByLabel('Name').fill('Pear');
    await page.getByLabel('Color').fill('Green');
    await page.getByRole('button', { name: 'Save Fruit' }).click();

    // Edit
    await page.getByRole('link', { name: 'Edit Fruit' }).click();
    await page.getByLabel('Color').fill('Yellow-Green');
    await page.getByRole('button', { name: 'Save Fruit' }).click();

    // Redirects to show page with updated values
    await expect(page.getByText('Fruit Details')).toBeVisible();
    await expect(page.locator('input[value="Yellow-Green"]')).toBeVisible();
});

test('admin can delete a fruit', async ({ page }) => {
    await loginAsAdmin(page);

    // Create
    await page.goto('/admin/fruits/create');
    await page.getByLabel('Name').fill('Kiwi');
    await page.getByLabel('Color').fill('Brown');
    await page.getByRole('button', { name: 'Save Fruit' }).click();

    // Go to list
    await page.goto('/admin/fruits');
    await expect(page.getByText('Kiwi')).toBeVisible();

    // Delete via row action
    const row = page.locator('tr', { has: page.getByText('Kiwi') });
    await row.getByRole('button', { name: 'Delete' }).click();

    await page.waitForURL('/admin/fruits');
    await expect(page.getByText('Kiwi')).not.toBeVisible();
});
