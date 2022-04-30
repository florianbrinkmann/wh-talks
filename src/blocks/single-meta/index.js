/* global whTalksMetas */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { useEntityProp } from '@wordpress/core-data';
import { SelectControl, TextControl, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

registerBlockType( 'wh-talks/single-meta', {
	edit: ( { attributes, setAttributes } ) => {
		const { metaKey, label } = attributes;
		const [ meta ] = useEntityProp( 'postType', 'talk', 'meta' );

		const currentMetaInfoEntry = whTalksMetas.find( ( metaInfo ) => {
			return metaInfo.key === metaKey;
		} );

		const selectOptions = whTalksMetas.map( ( metaInfo ) => {
			return {
				value: metaInfo.key,
				label: metaInfo.label,
			};
		} );

		let value = meta?.[ metaKey ];
		if ( value && metaKey.endsWith( '_link' ) ) {
			try {
				const url = new URL( value );
				value = (
					<a href="#" onClick={ ( e ) => e.preventDefault() }>
						{ url.host }
					</a>
				);
			} catch {
				value = __( 'No valid URL', 'wh-talks' );
			}
		}
		if ( ! value ) {
			/* translators: placeholder when no meta value exists. s = label for meta key */
			value = __( '»%s« meta value', 'wh-talks' ).replace(
				'%s',
				currentMetaInfoEntry.label
			);
		}

		let labelMarkup = '';
		if ( label ) {
			labelMarkup = (
				<span className="wh-talks-meta-label">{ label }</span>
			);
		}

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Talk meta settings', 'wh-talks' ) }>
						<SelectControl
							label={ __( 'Meta value to display', 'wh-talks' ) }
							value={ metaKey }
							options={ selectOptions }
							onChange={ ( metaKey ) =>
								setAttributes( { metaKey } )
							}
						/>
						<TextControl
							label={ __( 'Label', 'wh-talks' ) }
							value={ label }
							onChange={ ( label ) =>
								setAttributes( { label } )
							}
						/>
					</PanelBody>
				</InspectorControls>
				<p { ...useBlockProps() }>
					{ labelMarkup }
					{ value }
				</p>
			</>
		);
	},
	save: () => {
		return <p { ...useBlockProps.save() }/>;
	},
} );
