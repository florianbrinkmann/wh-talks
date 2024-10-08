import { registerPlugin } from '@wordpress/plugins';
import { PluginPostStatusInfo } from '@wordpress/editor';
import { useEntityProp } from '@wordpress/core-data';
import { CheckboxControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

registerPlugin( 'wh-talks-highlight-talk', {
	render: () => {
		if ( 'talk' !== wp.data.select( 'core/editor' ).getCurrentPostType() ) {
			return null;
		}
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
