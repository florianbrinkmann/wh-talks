/* global whTalksObject */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { useEntityProp } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';

registerBlockType( 'wh-talks/meta-list', {
	edit: () => {
		const [ meta ] = useEntityProp( 'postType', 'talk', 'meta' );

		let hasValues = false;

		return (
			<ul { ...useBlockProps() }>
				{ whTalksObject.metas.map( ( metaInfo, i ) => {
					let metaValue = meta?.[ metaInfo.key ];
					if ( ! metaValue ) {
						return false;
					}

					if ( metaInfo.key.endsWith( '_link' ) ) {
						try {
							const url = new URL( metaValue );
							metaValue = (
								<a href='#' onClick={ ( e ) => e.preventDefault() }>
									{ url.host }
								</a>
							);
						} catch {
							metaValue = <a>{ __(
								'This does not seem to be a valid URL',
								'wh-talks'
							) }</a>;
						}
					}
					hasValues = true;
					return (
						<li key={ i }>
							<span className='wh-talks-meta-label'>
								{ metaInfo.label }:
							</span>
							{ ' ' }
							{ metaValue }
						</li>
					);
				} ) }
				{ ! hasValues && <li>{ __( 'Lists all talk meta data.', 'wh-talks' ) }</li> }
			</ul>
		);
	}
} );
