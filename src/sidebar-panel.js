import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useEntityProp } from '@wordpress/core-data';

import { metaFields } from './fields';

registerPlugin( 'wh-hide-page-title-panel', {
	render: () => {
		if ( 'talk' !== wp.data.select('core/editor').getCurrentPostType() ) {
			return null;
		}
		const [ meta, setMeta ] = useEntityProp( 'postType', 'talk', 'meta' );

		const fields = metaFields( meta, setMeta );

		return (
			<PluginDocumentSettingPanel
				name='wh-talks-meta-panel'
				title='Talk meta data'
				icon='megaphone'
			>
				{ fields }
			</PluginDocumentSettingPanel>
		);
	},
} );
