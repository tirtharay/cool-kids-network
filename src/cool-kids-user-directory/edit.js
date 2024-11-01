import { __ } from '@wordpress/i18n';

export default function Edit() {
    return (
        <div>
            <h2>{__('User Directory - Frontend View Only', 'cool-kids-network')}</h2>
            <p>{__('The user directory will display all Cool Kids Network users.', 'cool-kids-network')}</p>
        </div>
    );
}
