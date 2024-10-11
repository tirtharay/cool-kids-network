import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

export default function Edit() {
    const blockProps = useBlockProps();

    return (
        <div {...blockProps}>
            <h3>{__('Cool Kids Signup', 'cool-kids-network')}</h3>
            <p>{__('This block displays a signup form on the frontend. To see it in action, view the page.', 'cool-kids-network')}</p>
        </div>
    );
}
