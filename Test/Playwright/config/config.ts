import coreConfig from '@loki-checkout/config';
import merge from '@loki/util/merge';

export type Theme = {
    name: string;
    theme: string;
    isHyva: boolean;
};

export const themes: Theme[] = [
    {name: 'Luma', theme: 'Magento/luma', isHyva: false},
    {name: 'Hyvä', theme: 'Loki/hyva-dev', isHyva: true},
];

export const withTheme = (config: any, theme: Theme) => merge(config, {theme: theme.theme});

export default merge(coreConfig, {
    modules: [
        'LokiCheckout_Hyva',
    ],
    config: {
    }
});
