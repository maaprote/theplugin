import { __ } from '@wordpress/i18n';
import { createRoot } from 'react-dom/client';
import { useState, useEffect } from 'react';
import { Card, CardBody, TextControl, ToggleControl, Button, Spinner, Popover, ColorPicker, RangeControl } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

const OptionsPage = () => {
	const [ isLoading, setIsLoading ] = useState( true );
	const [ isSaving, setIsSaving ] = useState( false );
	const [ firstName, setFirstName ] = useState( false );
	const [ lastName, setLastName ] = useState( false );
	const [ primaryColor, setPrimaryColor ] = useState( '#FFF' );
	const [ padding, setPadding ] = useState( 10 );
	const [ isPrimaryColorPopoverOpen, setIsPrimaryColorPopoverOpen ] = useState( false );
	const [ popoverAnchor, setPopoverAnchor ] = useState();

	useEffect( () => {
		const fetchData = async () => {
			try {
				const response = await apiFetch( { path: 'wp/v2/settings' } );
				
				setFirstName( response.rt_newsletter_form_display_first_name );
				setLastName( response.rt_newsletter_form_display_last_name );
				setPrimaryColor( response.rt_newsletter_form_primary_color );
				setPadding( response.rt_newsletter_form_fields_padding );

				setIsLoading( false );
			} catch (error) {
				console.error( error );
			}
		}

		fetchData();
	}, [] );

	// Save the settings.
	const submitHandler = async (e) => {
		e.preventDefault();

		setIsSaving( true );

		const data = {
			rt_newsletter_form_display_first_name: firstName,
			rt_newsletter_form_display_last_name: lastName,
			rt_newsletter_form_primary_color: primaryColor,
			rt_newsletter_form_fields_padding: padding
		};

		const fetchData = async () => {
			try {
				const response = await apiFetch( {
					path: 'wp/v2/settings',
					method: 'POST',
					data: data
				} );
			} catch (error) {
				console.error( error );
			}

			setIsSaving( false );
		}

		fetchData();
	}

	return (
		<div>
			<h1>{ __( '\'The Plugin\' Options Page', 'rt-theplugin' ) }</h1>
			<p>{ __( 'This is a very simple options page using WordPress blocks components', 'rt-theplugin' ) }</p>

			<form onSubmit={ submitHandler }>
				<Card style={ { maxWidth: '600px' } }>
					<CardBody>
						{ isLoading && <Spinner /> }
						{ ! isLoading && (
							<>
								<h2>{ __( 'Newsleter Form Settings', 'rt-theplugin' ) }</h2>
								<div style={ isSaving ? { pointerEvents: 'none', opacity: 0.7 } : {} }>
									<h3>{ __( 'General', 'rt-theplugin' ) }</h3>
									<ToggleControl
										label={ __( 'Display first name field', 'rt-theplugin' ) }
										checked={ firstName }
										onChange={ (newValue) => {
											setFirstName( newValue );
										} }
									/>
									<ToggleControl
										label={ __( 'Display last name field', 'rt-theplugin' ) }
										checked={ lastName }
										onChange={ (newValue) => {
											setLastName( newValue );
										} }
									/>

									<h3>{ __( 'Style', 'rt-theplugin' ) }</h3>
									<div style={ { display: 'flex', gap: 15, alignItems: 'center' } }>
										<p>{ __( 'Accent color', 'rt-theplugin' ) }</p>
										<div 
										style={ { 
											width: 20, 
											height: 20, 
											borderRadius: '100%', 
											backgroundColor: primaryColor, 
											borderStyle: 'solid', 
											borderWidth: 1, 
											borderColor: '#777', 
											cursor: 'pointer' 
										} }
										onClick={ (e) => {
											setIsPrimaryColorPopoverOpen( ! isPrimaryColorPopoverOpen );
											setPopoverAnchor( e.currentTarget );
										} }
										>
											{
												isPrimaryColorPopoverOpen && (
													<Popover anchor={ popoverAnchor }>
														<ColorPicker
															color={primaryColor}
															onChange={setPrimaryColor}
															enableAlpha
															defaultValue="#FFF"
														/>
													</Popover>
												)
											}
										</div>
									</div>
									<RangeControl
										label={ __( 'Fields Padding', 'rt-theplugin' ) }
										value={ padding }
										onChange={ ( value ) => setPadding( value ) }
										min={ 0 }
										max={ 50 }
									/>

								</div>
								<Button variant="primary" type="submit" style={ { marginTop: 15 } }>
									{ isSaving ? <Spinner /> : __( 'Save', 'rt-theplugin' )}
								</Button>
							</>
						) }
					</CardBody>
				</Card>
			</form>
		</div>
	);
};

const rootElement = document.getElementById( 'rt-theplugin-admin-page' );
if ( rootElement ) {
	createRoot( rootElement ).render( <OptionsPage /> );
}