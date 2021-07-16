import React from 'react';

import imageFieldData from './tabbed-item.yml';

// save in a global var for reuse in other components
window.styleguide.components.atoms.tabbed_item = {
  base: JSON.parse(JSON.stringify(imageFieldData))
};

/**
 * Storybook Definition.
 */
export default {
  title: 'Atoms/Tabbed item'
};
