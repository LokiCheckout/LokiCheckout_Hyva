import {Field, ShippingMethod, PaymentMethod, PlaceOrderButton, SuccessPage} from '@loki-checkout/checkout-objects';
import {setupCheckout} from '@loki/setup-checkout';
import {test, expect} from '@loki/test';
import moduleConfig, {themes, withTheme} from './config/config';

test.describe('Hyvä scripts test', () => {
    for (const theme of themes) {
        test(`should ${theme.isHyva ? '' : 'not '}load Hyvä scripts in the checkout (${theme.name})`, async ({page, context}) => {
            await setupCheckout(page, context, withTheme(moduleConfig, theme));

            const hasHyva = await page.evaluate(() => typeof (window as any).hyva === 'object');
            expect(hasHyva).toBe(theme.isHyva);

            if (theme.isHyva) {
                const hasAlpine = await page.evaluate(() => typeof (window as any).Alpine === 'object');
                expect(hasAlpine).toBe(true);
            }
        });

        test(`should allow me to checkout completely (${theme.name})`, async ({page, context}) => {
            await setupCheckout(page, context, withTheme(moduleConfig, theme));

            const fields = {
                'customer.email': 'jane@example.com',
                'shipping.firstname': 'Jane',
                'shipping.lastname': 'Doe',
                'shipping.company': 'Yireo',
                'shipping.city': 'Baarn',
                'shipping.street0': 'Amalialaan',
                'shipping.street1': '126',
                'shipping.telephone': '0123456789',
            };

            for (const [fieldName, fieldValue] of Object.entries(fields)) {
                const field = new Field(page, fieldName);
                await field.fill(fieldValue);
                await field.expectValue(fieldValue);
            }

            await (new ShippingMethod(page, 'flatrate_flatrate')).select();
            await (new PaymentMethod(page, 'checkmo')).select();
            await (new PlaceOrderButton(page)).click();

            await new SuccessPage(page).expectToBeLoaded();
        });
    }
});
