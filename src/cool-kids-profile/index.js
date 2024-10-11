import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import Edit from './edit';
import './style.scss';

// Register the block
registerBlockType('cool-kids-network/cool-kids-profile', {
    title: __('Cool Kids Profile', 'cool-kids-network'),
    description: __('Displays the user\'s profile information.', 'cool-kids-network'),
    category: 'widgets',
    icon: 'admin-users',
    supports: {
        html: false
    },
    edit: Edit,
    save: () => null // Rendered dynamically via PHP
});
