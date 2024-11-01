import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import Edit from './edit';
import './style.scss';

registerBlockType('cool-kids-network/cool-kids-user-directory', {
    title: __('Cool Kids User Directory', 'cool-kids-network'),
    icon: 'admin-users',
    category: 'widgets',
    edit: Edit,
    save: () => null, // Dynamic rendering in PHP
});
