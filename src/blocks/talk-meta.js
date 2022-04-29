import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { useEntityProp } from '@wordpress/core-data';
import { TextControl } from '@wordpress/components';

registerBlockType( 'wh-talks/meta', {
	edit: () => {
		const [ meta, setMeta ] = useEntityProp( 'postType', 'talk', 'meta' );

		const eventName = meta?.wh_talks_event_name,
			language = meta?.wh_talks_language,
			duration = meta?.wh_talks_duration;

		const updateMeta = ( metaKey, newValue ) => {
			setMeta( { ...meta, [ metaKey ]: newValue } );
		};

		return (
			<div { ...useBlockProps() }>
				<TextControl
					label='Name of event where you held the talk'
					value={ eventName }
					onChange={ ( newValue ) =>
						updateMeta( 'wh_talks_event_name', newValue )
					}
				/>
				<TextControl
					label='Language'
					value={ language }
					onChange={ ( newValue ) =>
						updateMeta( 'wh_talks_language', newValue )
					}
				/>
				<TextControl
					label='Duration'
					value={ duration }
					onChange={ ( newValue ) =>
						updateMeta( 'wh_talks_duration', newValue )
					}
				/>
			</div>
		);
	},
} );
