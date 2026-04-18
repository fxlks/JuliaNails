/**
 * SSA Booking Module - Entry point for Divi 5 Visual Builder
 *
 * @package Simply_Schedule_Appointments
 * @since 5.9.0
 */

import SSABookingModuleEdit from './SSABookingModule';
import { SSABookingSettingsContent } from './settings-content';
import metadata from '../module/module.json';
import { conversionOutline } from '../module/conversion-outline.js';

const { registerModule } = window.divi.moduleLibrary;
const { addAction } = window?.vendor?.wp?.hooks;

// Module configuration object for Divi 5
const ssaBookingConfig = {
	settings: {
		content: SSABookingSettingsContent,
	},
	renderers: {
		edit: SSABookingModuleEdit,
	},
	conversionOutline
};

addAction('divi.moduleLibrary.registerModuleLibraryStore.after', 'ssa', () => {
	registerModule(metadata, ssaBookingConfig);
});



