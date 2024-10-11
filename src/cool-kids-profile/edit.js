import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

export default function Edit() {
    const blockProps = useBlockProps();

    return (
        <div {...blockProps}>
            <h2>{__('Cool Kids Profile', 'cool-kids-network')}</h2>
            <p>{__('This block displays the profile information of the logged-in user on the frontend.', 'cool-kids-network')}</p>
        </div>
    );
}
