import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { useEntityProp } from '@wordpress/core-data';

import { metaFields } from './fields';

registerBlockType( 'wh-talks/meta', {
	edit: () => {
		const [ meta, setMeta ] = useEntityProp( 'postType', 'talk', 'meta' );

		const fields = metaFields( meta, setMeta );

		return <div { ...useBlockProps() }>{ fields }</div>;
	},
	save: () => {
		return <ul { ...useBlockProps.save() }></ul>;
	},
} );
