import {Field} from '@loki-checkout/checkout-objects';
import {setupCheckout} from '@loki/setup-checkout';
import {test, expect} from '@loki/test';
import merge from '@loki/util/merge';
import moduleConfig, {themes, withTheme} from './config/config';

const requiredFields = [
    'shipping.firstname',
    'shipping.lastname',
];

test.describe('Required block test', () => {
    for (const theme of themes) {
        test(`should not show a required block by default (${theme.name})`, async ({page, context}) => {
            await setupCheckout(page, context, withTheme(moduleConfig, theme));

            for (const fieldName of requiredFields) {
                const label = new Field(page, fieldName).getLabelLocator();
                await expect(label).toBeVisible();
                await expect(label.locator('sub')).toHaveCount(0);
            }
        });

        test(`should show a required block when enabled (${theme.name})`, async ({page, context}) => {
            const config = merge(moduleConfig, {
                config: {
                    'loki_field_components/fields/use_required_block': 1,
                }
            });

            await setupCheckout(page, context, withTheme(config, theme));

            for (const fieldName of requiredFields) {
                const label = new Field(page, fieldName).getLabelLocator();
                await expect(label).toBeVisible();
                await expect(label.locator('sub')).toHaveCount(1);
            }
        });
    }
});
