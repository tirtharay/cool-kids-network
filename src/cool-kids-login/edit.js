import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

export default function Edit() {
    const blockProps = useBlockProps();
    return (
        <div {...blockProps}>
            <h2>{__('Login', 'cool-kids-network')}</h2>
            <form>
                <input
                    type="email"
                    placeholder={__('Email Address', 'cool-kids-network')}
                    required
                />
                <button type="submit">{__('Login', 'cool-kids-network')}</button>
            </form>
        </div>
    );
}
