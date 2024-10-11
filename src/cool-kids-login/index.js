import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import edit from './edit';
import save from './save';
import './style.scss';

registerBlockType('cool-kids-network/cool-kids-login', {
    title: __('Cool Kids Login', 'cool-kids-network'),
    description: __('A login form for the Cool Kids Network.', 'cool-kids-network'),
    category: 'widgets',
    icon: 'lock',
    edit,
    save
});
