import { registerPlugin } from '@wordpress/plugins';
import { PluginPostStatusInfo } from '@wordpress/edit-post';
import { useEntityProp } from '@wordpress/core-data';
import { CheckboxControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

registerPlugin( 'wh-hide-page-title-checkbox-post-status-info', {
	render: () => {
		const [ meta, setMeta ] = useEntityProp( 'postType', 'talk', 'meta' );
		const updateMeta = ( newValue ) => {
			setMeta( { ...meta, wh_talks_is_highlight: newValue } );
		};
		const checked = meta?.wh_talks_is_highlight;
		return (
			<PluginPostStatusInfo>
				<CheckboxControl
					label={ __( 'Highlight', 'wh-talks' ) }
					checked={ checked }
					onChange={ () => {
						updateMeta( ! checked );
					} }
				/>
			</PluginPostStatusInfo>
		);
	},
} );
