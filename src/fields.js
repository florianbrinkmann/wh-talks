import { TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export const metaFields = ( meta, setMeta ) => {
	const eventName = meta?.wh_talks_event_name,
		language = meta?.wh_talks_language,
		duration = meta?.wh_talks_duration,
		videoLink = meta?.wh_talks_video_link,
		slidesLink = meta?.wh_talks_slides_link;

	const updateMeta = ( metaKey, newValue ) => {
		setMeta( { ...meta, [ metaKey ]: newValue } );
	};

	return (
		<>
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
		</>
	);
};
