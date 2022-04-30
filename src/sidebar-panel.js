import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useEntityProp } from '@wordpress/core-data';
import { TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

registerPlugin( 'wh-hide-page-title-panel', {
	render: () => {
		if ( 'talk' !== wp.data.select( 'core/editor' ).getCurrentPostType() ) {
			return null;
		}
		const [ meta, setMeta ] = useEntityProp( 'postType', 'talk', 'meta' );

		const eventName = meta?.wh_talks_event_name,
		language = meta?.wh_talks_language,
		duration = meta?.wh_talks_duration,
		videoLink = meta?.wh_talks_video_link,
		slidesLink = meta?.wh_talks_slides_link;

		const updateMeta = ( metaKey, newValue ) => {
			setMeta( { ...meta, [ metaKey ]: newValue } );
		};

		return (
			<PluginDocumentSettingPanel
				name='wh-talks-meta-panel'
				title='Talk meta data'
				icon='megaphone'
			>
				<TextControl
					label={ __(
						'Name of event where you held the talk',
						'wh-talks'
					) }
					value={ eventName }
					onChange={ ( newValue ) =>
						updateMeta( 'wh_talks_event_name', newValue )
					}
				/>
				<TextControl
					label={ __( 'Language' ) }
					value={ language }
					onChange={ ( newValue ) =>
						updateMeta( 'wh_talks_language', newValue )
					}
				/>
				<TextControl
					label={ __( 'Duration' ) }
					value={ duration }
					onChange={ ( newValue ) =>
						updateMeta( 'wh_talks_duration', newValue )
					}
				/>
				<TextControl
					label={ __( 'Video link' ) }
					value={ videoLink }
					type='url'
					onChange={ ( newValue ) =>
						updateMeta( 'wh_talks_video_link', newValue )
					}
				/>
				<TextControl
					label={ __( 'Slides link' ) }
					value={ slidesLink }
					type='url'
					onChange={ ( newValue ) =>
						updateMeta( 'wh_talks_slides_link', newValue )
					}
				/>
			</PluginDocumentSettingPanel>
		);
	},
} );
