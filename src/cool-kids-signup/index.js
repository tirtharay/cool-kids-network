import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import Edit from './edit';
import './style.scss';

// Register the block
registerBlockType('cool-kids-network/cool-kids-signup', {
    title: __('Cool Kids Signup', 'cool-kids-network'),
    description: __('A signup form for the Cool Kids Network.', 'cool-kids-network'),
    category: 'widgets',
    icon: 'email',
    supports: {
        html: false
    },
    edit: Edit,
    save: () => null // Rendered dynamically via PHP
});
