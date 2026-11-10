/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 677
(module) {

module.exports = (function() { return this["React"]; }());

/***/ },

/***/ 631
(module) {

module.exports = (function() { return this["wp"]["apiFetch"]; }());

/***/ },

/***/ 959
(module) {

module.exports = (function() { return this["wp"]["components"]; }());

/***/ },

/***/ 488
(module) {

module.exports = (function() { return this["wp"]["coreData"]; }());

/***/ },

/***/ 987
(module) {

module.exports = (function() { return this["wp"]["data"]; }());

/***/ },

/***/ 2
(module) {

module.exports = (function() { return this["wp"]["editor"]; }());

/***/ },

/***/ 601
(module) {

module.exports = (function() { return this["wp"]["element"]; }());

/***/ },

/***/ 989
(module) {

module.exports = (function() { return this["wp"]["htmlEntities"]; }());

/***/ },

/***/ 75
(module) {

module.exports = (function() { return this["wp"]["i18n"]; }());

/***/ },

/***/ 672
(module) {

module.exports = (function() { return this["wp"]["notices"]; }());

/***/ },

/***/ 125
(module) {

module.exports = (function() { return this["wp"]["plugins"]; }());

/***/ },

/***/ 933
(module) {

module.exports = (function() { return this["wp"]["primitives"]; }());

/***/ },

/***/ 172
(module) {

module.exports = (function() { return this["wp"]["url"]; }());

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	const __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		const cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		const module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = (module) => {
/******/ 		const getter = module && module.__esModule ?
/******/ 			() => (module['default']) :
/******/ 			() => (module);
/******/ 		__webpack_require__.d(getter, { a: getter });
/******/ 		return getter;
/******/ 	};
/******/ 	
/******/ 	/* webpack/runtime/concatenation wrap */
/******/ 	// wrap a concatenated module body as a lazy, memoized accessor; mod is
/******/ 	// set before the body runs so re-entrant calls (require cycles) observe
/******/ 	// the partial exports like Node.js
/******/ 	__webpack_require__.cw = (body) => {
/******/ 		var mod;
/******/ 		return () => {
/******/ 			if (body) {
/******/ 				var fn = body;
/******/ 				body = 0;
/******/ 				mod = { exports: {} };
/******/ 				fn.call(mod.exports, mod, mod.exports);
/******/ 			}
/******/ 			return mod.exports;
/******/ 		};
/******/ 	};
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	// define getter/value functions for harmony exports
/******/ 	__webpack_require__.d = (exports, definition) => {
/******/ 		for(var key in definition) {
/******/ 			if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 				Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 			}
/******/ 		}
/******/ 	};
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop));
/******/ 	
/************************************************************************/
let __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";

// MODULE: ./node_modules/react/cjs/react-jsx-runtime.production.min.js
var react_jsx_runtime_production_min_namespaceFn = /*#__PURE__*/__webpack_require__.cw(function(module, exports) {
/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */
var f=(external_React_namespaceFn()),k=Symbol.for("react.element"),l=Symbol.for("react.fragment"),m=Object.prototype.hasOwnProperty,n=f.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,p={key:!0,ref:!0,__self:!0,__source:!0};
function q(c,a,g){var b,d={},e=null,h=null;void 0!==g&&(e=""+g);void 0!==a.key&&(e=""+a.key);void 0!==a.ref&&(h=a.ref);for(b in a)m.call(a,b)&&!p.hasOwnProperty(b)&&(d[b]=a[b]);if(c&&c.defaultProps)for(b in a=c.defaultProps,a)void 0===d[b]&&(d[b]=a[b]);return{$$typeof:k,type:c,key:e,ref:h,props:d,_owner:n.current}}exports.Fragment=l;exports.jsx=q;exports.jsxs=q;

});

// MODULE: ./node_modules/react/jsx-runtime.js
var jsx_runtime_namespaceFn = /*#__PURE__*/__webpack_require__.cw(function(module, exports) {


if (true) {
  module.exports = (react_jsx_runtime_production_min_namespaceFn());
} else // removed by dead control flow
{}

});

// EXTERNAL MODULE: external "React"
var external_React_namespaceFn = () => {
	return __webpack_require__(677);
};

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__(2);
// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(601);
// EXTERNAL MODULE: external {"this":["wp","apiFetch"]}
var external_this_wp_apiFetch_ = __webpack_require__(631);
var external_this_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_apiFetch_);
;// ./node_modules/@wpsyntex/polylang-react-library/build/middlewares/filter-path.js
/**
 * Filters requests for translatable entities.
 * This logic is shared across all Polylang plugins.
 *
 * @since 3.5
 *
 * @param {Object}   options        - API fetch options object.
 * @param {Array}    filteredRoutes - Array of route paths to filter.
 * @param {Function} filter         - Function to filter matching routes.
 * @return {Object} Modified REST request options.
 */
const filterPathMiddleware = (options, filteredRoutes, filter) => {
  const cleanPath = options.path.split('?')[0].replace(/^\/+|\/+$/g, ''); // Get path without query parameters and trim '/'.

  return Object.values(filteredRoutes).find(path => cleanPath === path) ? filter(options) : options;
};
/* harmony default export */ const filter_path = (filterPathMiddleware);
;// ./node_modules/@wpsyntex/polylang-react-library/build/middlewares/editors-requests-filter.js
/**
 * WordPress dependencies.
 */


/*
 * Internal dependencies.
 */


/**
 * Safely filters requests for translatable entities in block editor type screens.
 * Ensures that `pllFilteredRoutes` has been well defined on server side and
 * that the filtered request is a REST one.
 *
 * @param {Function} filterCallback - Function to filter API fetch options.
 */
const editorsRequestsFilter = filterCallback => {
  external_this_wp_apiFetch_default().use((options, next) => {
    /*
     * If options.url is defined, this is not a REST request but a direct call to post.php for legacy metaboxes.
     * If `filteredRoutes` is not defined, return early.
     */
    if ('undefined' !== typeof options.url || 'undefined' === typeof window.pllFilteredRoutes) {
      return next(options);
    }
    return next(filter_path(options, window.pllFilteredRoutes, filterCallback));
  });
};
/* harmony default export */ const editors_requests_filter = (editorsRequestsFilter);
// EXTERNAL MODULE: external {"this":["wp","plugins"]}
var external_this_wp_plugins_ = __webpack_require__(125);
// EXTERNAL MODULE: external {"this":["wp","primitives"]}
var external_this_wp_primitives_ = __webpack_require__(933);
;// ./node_modules/react/jsx-runtime.js
jsx_runtime_namespaceFn();

;// ./node_modules/@wpsyntex/polylang-react-library/build/icons/translation.js
/**
 * Translation icon - translation Dashicon.
 */

/**
 * WordPress dependencies
 */


const isPrimitivesComponents = 'undefined' !== typeof wp.primitives;
const translation = isPrimitivesComponents ? /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_primitives_.SVG, {
  width: "20",
  height: "20",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 20 20",
  children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_primitives_.Path, {
    d: "M11 7H9.49c-.63 0-1.25.3-1.59.7L7 5H4.13l-2.39 7h1.69l.74-2H7v4H2c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h7c1.1 0 2 .9 2 2v2zM6.51 9H4.49l1-2.93zM10 8h7c1.1 0 2 .9 2 2v7c0 1.1-.9 2-2 2h-7c-1.1 0-2-.9-2-2v-7c0-1.1.9-2 2-2zm7.25 5v-1.08h-3.17V9.75h-1.16v2.17H9.75V13h1.28c.11.85.56 1.85 1.28 2.62-.87.36-1.89.62-2.31.62-.01.02.22.97.2 1.46.84 0 2.21-.5 3.28-1.15 1.09.65 2.48 1.15 3.34 1.15-.02-.49.2-1.44.2-1.46-.43 0-1.49-.27-2.38-.63.7-.77 1.14-1.77 1.25-2.61h1.36zm-3.81 1.93c-.5-.46-.85-1.13-1.01-1.93h2.09c-.17.8-.51 1.47-1 1.93l-.04.03s-.03-.02-.04-.03z"
  })
}) : 'translation';
/* harmony default export */ const icons_translation = (translation);
;// ./js/src/editors/common/app.js
/**
 * WordPress dependencies
 */


/**
 * External dependencies
 */


const App = ({
  sidebar,
  sidebarName,
  onPromise,
  children
}) => {
  onPromise().then(() => {
    (0,external_this_wp_plugins_.registerPlugin)(sidebarName, {
      icon: icons_translation,
      render: sidebar
    });
  }, reason => {
    console.info(reason); // eslint-disable-line no-console
  });
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)((react_jsx_runtime_production_min_namespaceFn().Fragment), {
    children: children
  });
};
/* harmony default export */ const app = (App);
// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(987);
// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(959);
// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(75);
;// ./node_modules/@wpsyntex/polylang-react-library/build/icons/plus.js
/**
 * Plus icon - plus Dashicon.
 */

/**
 * WordPress dependencies
 */


const plus_isPrimitivesComponents = 'undefined' !== typeof wp.primitive;
const plus = plus_isPrimitivesComponents ? /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_primitives_.SVG, {
  width: "20",
  height: "20",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 20 20",
  children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_primitives_.Path, {
    d: "M17 7v3h-5v5h-3v-5h-5v-3h5v-5h3v5h5z"
  })
}) : 'plus';
/* harmony default export */ const icons_plus = (plus);
;// ./js/src/editors/common/components/buttons/add/index.js
/**
 * WordPress dependencies
 */



/**
 * External dependencies
 */


/**
 * Renders a button to add new translation.
 *
 * @param {Object}   props                Component props.
 * @param {Object}   props.language       Language of the new translation.
 * @param {string}   props.href           URL to add a new translation, pass '#' if managed in REST.
 * @param {boolean}  props.disabled       Whether the button is disabled.
 * @param {Function} props.handleAddClick Callback to add a translation, default to null. Useful only if the button is not a link.
 * @return {React.ReactElement} Button component.
 */

const AddButton = ({
  language,
  href,
  disabled,
  handleAddClick = null
}) => {
  const accessibilityText = (0,external_this_wp_i18n_.sprintf)(
  // translators: %s is a native language name.
  (0,external_this_wp_i18n_.__)('Add a translation in %s', 'polylang-pro'), language.name);
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.Button, {
    href: href,
    disabled: disabled,
    icon: icons_plus,
    label: accessibilityText,
    className: `pll-button`,
    onClick: handleAddClick ? event => {
      event.preventDefault();
      handleAddClick(event);
    } : undefined,
    "data-target-language": language.slug // Store the target language to retrieve it through the click event.
    ,
    children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("span", {
      className: "screen-reader-text",
      children: accessibilityText
    })
  });
};
/* harmony default export */ const add = (AddButton);
;// ./js/src/editors/common/components/buttons/delete/index.js
/* unused harmony import specifier */ var Button;
/* unused harmony import specifier */ var sprintf;
/* unused harmony import specifier */ var __;
/* unused harmony import specifier */ var trash;
/* unused harmony import specifier */ var _jsx;
/**
 * WordPress dependencies
 */



/**
 * External dependencies
 */


/**
 * Renders a button to delete a translation.
 *
 * @param {Object}   props          Component props.
 * @param {Object}   props.language Language of the existing translation.
 * @param {boolean}  props.disabled Whether the button is disabled.
 * @param {Function} props.onClick  Callback to delete a translation.
 * @return {React.ReactElement} Button component.
 */

const DeleteButton = ({
  language,
  disabled,
  onClick
}) => {
  const translationScreenReaderText = sprintf(
  // translators: %s is a native language name.
  __('Delete the translation in %s', 'polylang-pro'), language.name);
  return /*#__PURE__*/_jsx(Button, {
    icon: trash,
    label: translationScreenReaderText,
    disabled: disabled,
    className: "pll-button",
    onClick: onClick,
    children: /*#__PURE__*/_jsx("span", {
      className: "screen-reader-text",
      children: translationScreenReaderText
    })
  });
};
/* harmony default export */ const buttons_delete = ((/* unused pure expression or super */ null && (DeleteButton)));
;// ./js/src/editors/common/settings.js
/**
 * Module Constants
 */

const MODULE_KEY = 'pll/metabox';
const MODULE_CORE_EDITOR_KEY = 'core/editor';
const MODULE_SITE_EDITOR_KEY = 'core/edit-site';
const MODULE_POST_EDITOR_KEY = 'core/edit-post';
const MODULE_CORE_KEY = 'core';
const DEFAULT_STATE = {
  languages: [],
  selectedLanguage: {},
  translatedPosts: {},
  fromPost: null,
  currentTemplatePart: {}
};
const UNTRANSLATABLE_POST_TYPE = ['wp_template', 'wp_global_styles'];
const POST_TYPE_WITH_TRASH = (/* unused pure expression or super */ null && (['page']));
const TEMPLATE_PART_SLUG_SEPARATOR = '___'; // Its value must be synchronized with its equivalent in PHP @see PLL_FSE_Template_Slug::SEPARATOR.
const TEMPLATE_PART_SLUG_CHECK_LANGUAGE_PATTERN = '[a-z][a-z0-9_-]*'; // Its value must be synchronized with it equivalent in PHP @see PLL_FSE_Template_Slug::SEPARATOR.

// EXTERNAL MODULE: external {"this":["wp","coreData"]}
var external_this_wp_coreData_ = __webpack_require__(488);
// EXTERNAL MODULE: external {"this":["wp","notices"]}
var external_this_wp_notices_ = __webpack_require__(672);
;// ./js/src/editors/common/components/buttons/persisting-user-data/index.js
/**
 * WordPress dependencies
 */







/**
 * Prepares user preference data for API call.
 *
 * @param {Object}  currentUser        The current user object.
 * @param {string}  userPreferenceName The user preference name.
 * @param {string}  postType           The post type.
 * @param {boolean} isActive           Current active state.
 * @return {Object} Prepared data for API call.
 */

const prepareUserPreferenceData = (currentUser, userPreferenceName, postType, isActive) => {
  /*
   * If the user meta is an empty array, it has never been created.
   * So we convert it as an object to be able to update correctly its value in DB.
   */
  if (undefined === currentUser[userPreferenceName] || Array.isArray(currentUser[userPreferenceName]) && currentUser[userPreferenceName].length === 0) {
    currentUser[userPreferenceName] = {};
  }

  // Updates currentUser preference.
  currentUser[userPreferenceName][postType] = !isActive;
  return {
    [userPreferenceName]: currentUser[userPreferenceName]
  };
};

/**
 * Button to persist user data per post type.
 *
 * @param {Object} props                    The component props.
 * @param {string} props.id                 The button id attribute.
 * @param {string} props.postType           The post type.
 * @param {string} props.userPreferenceName The user preference name.
 * @param {string} props.activeLabel        The active label.
 * @param {string} props.inactiveLabel      The inactive label.
 * @param {Object} props.icon               The icon.
 *
 * @return {React.ReactNode} The Persisting User Data Button.
 */
const PersistingUserDataButton = ({
  id,
  postType,
  userPreferenceName,
  activeLabel,
  inactiveLabel,
  icon
}) => {
  const currentUser = (0,external_this_wp_data_.useSelect)(select => select(external_this_wp_coreData_.store).getCurrentUser(), []);
  const [isLoading, setIsLoading] = (0,external_this_wp_element_.useState)(false);
  const {
    createErrorNotice
  } = (0,external_this_wp_data_.useDispatch)(external_this_wp_notices_.store);
  const buttonInitialState = () => {
    if (undefined === currentUser || undefined === currentUser[userPreferenceName] || undefined === currentUser[userPreferenceName][postType]) {
      return false;
    }
    return currentUser[userPreferenceName][postType];
  };
  const [isActive, setIsActive] = (0,external_this_wp_element_.useState)(buttonInitialState);
  const saveStateInUserPreferences = () => {
    setIsLoading(true);
    const data = prepareUserPreferenceData(currentUser, userPreferenceName, postType, isActive);

    // Saves the preference.
    external_this_wp_apiFetch_default()({
      path: '/wp/v2/users/me',
      data,
      method: 'POST'
    }).then(() => {
      // Update component state only on successful API call.
      setIsActive(prevIsActive => !prevIsActive);
    }).catch(error => {
      createErrorNotice(error.message, {
        type: 'snackbar'
      });
    }).finally(() => {
      // Always reset loading state.
      setIsLoading(false);
    });
  };
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.Button, {
    id: id,
    className: `pll-button pll-before-post-translations-button ${isActive && `wp-ui-text-highlight`}`,
    onClick: saveStateInUserPreferences,
    icon: icon,
    label: isActive ? activeLabel : inactiveLabel,
    isBusy: isLoading,
    disabled: isLoading,
    children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("span", {
      className: "screen-reader-text",
      children: isActive ? activeLabel : inactiveLabel
    })
  });
};
/* harmony default export */ const persisting_user_data = (PersistingUserDataButton);
;// ./js/src/editors/common/components/buttons/machine-translation/index.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



/**
 * Machine Translation Button
 *
 * @return {React.ReactNode|null} The Machine Translation Button, `null` if disabled.
 */

const MachineTranslationButton = () => {
  // Hook must run before the early return below.
  // eslint-disable-next-line @wordpress/no-unused-vars-before-return
  const postType = (0,external_this_wp_data_.useSelect)(select => select(MODULE_CORE_EDITOR_KEY).getCurrentPostType(), []);
  const machineTranslation = pll_block_editor_plugin_settings?.machine_translation;
  if (!machineTranslation || !machineTranslation.isActive) {
    return null;
  }
  const {
    path_d,
    ...iconProps
  } = machineTranslation.icon;
  const icon = {
    type: 'svg',
    props: {
      ...iconProps,
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_primitives_.Path, {
        d: path_d
      }) // eslint-disable-line camelcase
    }
  };
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(persisting_user_data, {
    id: 'pll-machine-translation',
    postType,
    userPreferenceName: `pll_machine_translation_${machineTranslation.slug}`,
    activeLabel: (0,external_this_wp_i18n_.sprintf)(/* translators: %s is the name of the machine translation service. */
    (0,external_this_wp_i18n_.__)('Deactivate %s machine translation', 'polylang-pro'), machineTranslation.name),
    inactiveLabel: (0,external_this_wp_i18n_.sprintf)(/* translators: %s is the name of the machine translation service. */
    (0,external_this_wp_i18n_.__)('Activate %s machine translation', 'polylang-pro'), machineTranslation.name),
    icon
  });
};
/* harmony default export */ const machine_translation = (MachineTranslationButton);
;// ./node_modules/@wpsyntex/polylang-react-library/build/icons/duplication.js
/**
 * Duplication icon - admin-page Dashicon.
 */

/**
 * WordPress dependencies
 */


const duplication_isPrimitivesComponents = 'undefined' !== typeof wp.primitives;
const duplication = duplication_isPrimitivesComponents ? /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_primitives_.SVG, {
  width: "20",
  height: "20",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 20 20",
  children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_primitives_.Path, {
    d: "M6 15v-13h10v13h-10zM5 16h8v2h-10v-13h2v11z"
  })
}) : 'admin-page';
/* harmony default export */ const icons_duplication = (duplication);
;// ./js/src/editors/common/components/buttons/duplicate/index.js
/**
 * WordPress dependencies
 */



/**
 * External dependencies
 */


/**
 * Internal dependencies
 */



/**
 * Duplicate Button
 *
 * @param {Object} props The component props.
 *
 * @return {React.ReactNode|null} The Duplicate Button, `null` if loading.
 */

const DuplicateButton = props => {
  const postType = (0,external_this_wp_data_.useSelect)(select => select(MODULE_CORE_EDITOR_KEY).getCurrentPostType(), []);
  if (!postType) {
    return null;
  }
  const newProps = {
    ...props,
    id: 'pll-duplicate',
    userPreferenceName: 'pll_duplicate_content',
    /* translators: accessibility text */
    activeLabel: (0,external_this_wp_i18n_.__)('Deactivate the content duplication', 'polylang-pro'),
    /* translators: accessibility text */
    inactiveLabel: (0,external_this_wp_i18n_.__)('Activate the content duplication', 'polylang-pro'),
    icon: icons_duplication,
    postType
  };
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(persisting_user_data, {
    ...newProps
  });
};
/* harmony default export */ const duplicate = (DuplicateButton);
;// ./node_modules/@wpsyntex/polylang-react-library/build/icons/pencil.js
/**
 * Pencil icon - edit Dashicon.
 */

/**
 * WordPress dependencies
 */


const pencil_isPrimitivesComponents = 'undefined' !== typeof wp.primitives;
const pencil = pencil_isPrimitivesComponents ? /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_primitives_.SVG, {
  width: "20",
  height: "20",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 20 20",
  children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_primitives_.Path, {
    d: "M13.89 3.39l2.71 2.72c0.46 0.46 0.42 1.24 0.030 1.64l-8.010 8.020-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.030c0.39-0.39 1.22-0.39 1.68 0.070zM11.16 6.18l-5.59 5.61 1.11 1.11 5.54-5.65zM8.19 14.41l5.58-5.6-1.070-1.080-5.59 5.6z"
  })
}) : 'edit';
/* harmony default export */ const icons_pencil = (pencil);
;// ./js/src/editors/common/components/buttons/edit/index.js
/**
 * WordPress dependencies
 */



/**
 * External dependencies
 */


/**
 * Renders a button to edit existing translation.
 *
 * @param {Object}   props                 Component props.
 * @param {Object}   props.language        Language of the existing translation.
 * @param {string}   props.href            URL to edit a new translation, pass '#' if managed in REST.
 * @param {boolean}  props.disabled        Whether the button is disabled.
 * @param {Function} props.handleEditClick Callback to edit a translation, default to null. Useful only if the button is not a link.
 * @return {React.ReactElement} Button component.
 */

const EditButton = ({
  language,
  href,
  disabled,
  handleEditClick = null
}) => {
  const accessibilityText = (0,external_this_wp_i18n_.sprintf)(/* translators: accessibility text, %s is a native language name. For example Deutsch for German or Français for french. */
  (0,external_this_wp_i18n_.__)('Edit the translation in %s', 'polylang-pro'), language.name);
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.Button, {
    href: href,
    disabled: disabled,
    icon: icons_pencil,
    label: accessibilityText,
    className: `pll-button`,
    onClick: handleEditClick ? event => {
      event.preventDefault();
      handleEditClick(event);
    } : undefined,
    "data-target-language": language.slug // Store the target language to retrieve it through the click event.
    ,
    children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("span", {
      className: "screen-reader-text",
      children: accessibilityText
    })
  });
};
/* harmony default export */ const edit = (EditButton);
;// ./node_modules/@wpsyntex/polylang-react-library/build/icons/synchronization.js
/**
 * Synchronization icon - controls-repeat Dashicon.
 */

/**
 * WordPress dependencies
 */


const synchronization_isPrimitivesComponents = 'undefined' !== typeof wp.primitives;
const synchronization = synchronization_isPrimitivesComponents ? /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_primitives_.SVG, {
  width: "20",
  height: "20",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 20 20",
  children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_primitives_.Path, {
    d: "M5 7v3l-2 1.5v-6.5h11v-2l4 3.010-4 2.99v-2h-9zM15 13v-3l2-1.5v6.5h-11v2l-4-3.010 4-2.99v2h9z"
  })
}) : 'controls-repeat';
/* harmony default export */ const icons_synchronization = (synchronization);
;// ./js/src/editors/common/components/buttons/synchronization/use-dispatch-current-post-title.js
/**
 * WordPress dependencies
 */


/**
 * Custom hook to dispatch the current post title to the translation table reducer.
 *
 * @param {Object}      language                The language object.
 * @param {Function}    translationTableReducer The translation table reducer.
 * @param {Object}      sourcePost              The source post object.
 * @param {Object|null} post                    The post object, null when no translation.
 * @return {void}
 */
const useDispatchCurrentPostTitle = (language, translationTableReducer, sourcePost, post) => {
  (0,external_this_wp_element_.useEffect)(() => {
    if (sourcePost.pll_sync_post[language.slug] && sourcePost.translations[language.slug] && sourcePost.title !== post?.title) {
      const translatedPost = {
        ...sourcePost,
        // Keep synchronized post title up to date.
        title: sourcePost.title
      };
      translatedPost.id = post?.id ?? 0;
      translationTableReducer({
        type: 'add_translation',
        lang: language,
        post: {
          ...translatedPost
        }
      });
    }
  }, [language, translationTableReducer, sourcePost, post]);
};
/* harmony default export */ const use_dispatch_current_post_title = (useDispatchCurrentPostTitle);
;// ./js/src/editors/common/components/buttons/synchronization/index.js
/**
 * WordPress dependencies
 */






/**
 * External dependencies
 */


/**
 * Internal dependencies
 */



/**
 * Prepares the sync posts object to be stored with a REST field.
 *
 * @param {Object} syncPosts The sync posts object.
 * @return {Object} The prepared sync posts object, with boolean values converted to strings.
 */

const prepareSyncPosts = syncPosts => {
  return Object.fromEntries(Object.entries(syncPosts).map(([key, value]) => [key, value.toString()]));
};

/**
 * Synchronization button component.
 *
 * @param {Object}  props                         The props object.
 * @param {Object}  props.post                    The post object.
 * @param {Object}  props.language                The language object.
 * @param {Object}  props.translationTableReducer The translation table reducer.
 * @param {boolean} props.disabled                Whether the button is disabled.
 * @return {React.Component} The synchronization button component.
 */
const SynchronizationButton = ({
  post,
  language,
  translationTableReducer,
  disabled
}) => {
  const [isModalOpen, setIsModalOpen] = (0,external_this_wp_element_.useState)(false);
  const sourcePost = (0,external_this_wp_data_.useSelect)(selectFn => selectFn(MODULE_CORE_EDITOR_KEY).getCurrentPost(), []);
  const isSynchronized = (0,external_this_wp_data_.useSelect)(selectFn => {
    const syncPosts = selectFn(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('pll_sync_post');
    return syncPosts?.[language.slug] !== undefined;
  }, [language.slug]);
  const isTranslated = post?.id !== undefined;
  use_dispatch_current_post_title(language, translationTableReducer, sourcePost, post);
  const buttonLabel = (0,external_this_wp_element_.useMemo)(() => {
    return isSynchronized ? (0,external_this_wp_i18n_.__)('Unsynchronize this post', 'polylang-pro') : (0,external_this_wp_i18n_.__)('Synchronize this post', 'polylang-pro');
  }, [isSynchronized]);
  const toggleSynchronizationStatus = () => {
    const syncPosts = {
      ...((0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getEditedPostAttribute('pll_sync_post') ?? {})
    };
    if (isSynchronized) {
      delete syncPosts[language.slug];
    } else {
      syncPosts[language.slug] = true;
      const translatedPost = post ?? {
        id: 0
      };
      translationTableReducer({
        type: 'add_translation',
        lang: language,
        post: {
          // Optimistic rendering.
          ...translatedPost,
          title: sourcePost.title
        }
      });
      (0,external_this_wp_data_.dispatch)(external_this_wp_coreData_.store).invalidateResolution('getEntityRecord', ['postType', translatedPost.type, translatedPost.id]);
    }
    ;(0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).editPost({
      pll_sync_post: prepareSyncPosts(syncPosts)
    });
  };
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)((react_jsx_runtime_production_min_namespaceFn().Fragment), {
    children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.Button, {
      icon: icons_synchronization,
      label: buttonLabel,
      id: `pll_sync_post[${language.slug}]`,
      className: `pll-button ${isSynchronized && 'wp-ui-text-highlight'}`,
      onClick: () => {
        if (!isSynchronized && isTranslated) {
          setIsModalOpen(true);
        } else {
          toggleSynchronizationStatus();
        }
      },
      disabled: disabled,
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("span", {
        className: "screen-reader-text",
        children: buttonLabel
      })
    }), isModalOpen && /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)(external_this_wp_components_.Modal, {
      title: (0,external_this_wp_i18n_.__)('Synchronize this post', 'polylang-pro'),
      onRequestClose: () => {
        setIsModalOpen(false);
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("p", {
        children: (0,external_this_wp_i18n_.__)('You are about to overwrite an existing translation. Are you sure you want to proceed?', 'polylang-pro')
      }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)("div", {
        role: "group",
        className: "components-button-group buttons",
        children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.Button, {
          variant: "tertiary",
          onClick: () => {
            setIsModalOpen(false);
          },
          type: "button",
          children: (0,external_this_wp_i18n_.__)('Cancel', 'polylang-pro')
        }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("span", {
          children: "\xA0"
        }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.Button, {
          variant: "primary",
          onClick: () => {
            toggleSynchronizationStatus();
            setIsModalOpen(false);
          },
          type: "submit",
          children: (0,external_this_wp_i18n_.__)('Synchronize', 'polylang-pro')
        })]
      })]
    })]
  });
};
/* harmony default export */ const buttons_synchronization = (SynchronizationButton);
;// ./js/src/editors/common/components/buttons/index.js
/**
 * Buttons components.
 */







;// ./js/src/editors/common/components/plugin-feature-flag-button/index.js
/**
 * Defines as extensibility slot for the metabox .
 */

/**
 * WordPress dependencies
 */


const {
  Fill,
  Slot
} = (0,external_this_wp_components_.createSlotFill)('PluginFeatureFlagButton');

/**
 * Plugin feature flag button.
 *
 * @example
 * ```js
 * import { registerPlugin } from '@wordpress/plugins';
 * import { PluginFeatureFlagButton } from '@wpsyntex/polylang';
 * import { YourCustomButton } from './your-custom-button';
 *
 * registerPlugin( 'pll-plugin-feature-flag-button', {
 * 	render: () => (
 * 		<PluginFeatureFlagButton>
 * 			<YourCustomButton />
 * 		</PluginFeatureFlagButton>
 * 	),
 * } );
 * ```
 *
 * @param {Object}          props           The component props.
 * @param {React.ReactNode} props.children  The children.
 * @param {string}          props.className The class name.
 *
 * @return {React.ReactNode} The Plugin Feature Flag Button.
 */
const PluginFeatureFlagButton = ({
  children,
  className
}) => /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(Fill, {
  children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("div", {
    className: className,
    children: children
  })
});
PluginFeatureFlagButton.Slot = Slot;
/* harmony default export */ const plugin_feature_flag_button = (PluginFeatureFlagButton);
;// ./js/src/editors/common/components/translations-table/plugin-feature-table-row-button/index.js
/**
 * Defines as extensibility slot for the translations table row.
 */

/**
 * WordPress dependencies
 */



const createSlotFillWithId = id => (0,external_this_wp_components_.createSlotFill)(`PluginFeatureTableRowButton_${id}`);

/**
 * Plugin feature table row button fill.
 * Ideally one per language.
 *
 * @example
 * ```js
 * import { registerPlugin } from '@wordpress/plugins';
 * import { PluginFeatureTableRowButtonFill } from '@wpsyntex/polylang';
 * import { YourCustomButton } from './your-custom-button';
 *
 * // You may want to loop through the languages and register one fill per language.
 * registerPlugin( 'pll-plugin-feature-table-row-button-fill', {
 * 	render: () => (
 * 		<PluginFeatureTableRowButtonFill languageSlug="en">
 * 			<YourCustomButton />
 * 		</PluginFeatureTableRowButtonFill>
 * 	),
 * } );
 * ```
 *
 * @param {Object}          props              The component props.
 * @param {React.ReactNode} props.children     The children.
 * @param {string}          props.className    The class name.
 * @param {string}          props.languageSlug The language slug, used to generate a unique slot fill.
 *
 * @return {React.ReactNode} The plugin feature table row button fill.
 */
const PluginFeatureTableRowButtonFill = ({
  children,
  className,
  languageSlug
}) => {
  const {
    Fill
  } = createSlotFillWithId(languageSlug);
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(Fill, {
    children: drilledProps => {
      return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("div", {
        className: className,
        children: external_this_wp_element_.Children.map(children, child => (0,external_this_wp_element_.cloneElement)(child, {
          language: drilledProps.language,
          translation: drilledProps.translation,
          translationTableReducer: drilledProps.translationTableReducer
        }))
      });
    }
  });
};

/**
 * Plugin feature table row button slot.
 * Ideally one per language.
 *
 * @param {Object} props                         The component props.
 * @param {string} props.language                The language object, used to generate a unique slot fill.
 * @param {Object} props.translation             The translated object.
 * @param {Object} props.translationTableReducer The table dispatch object.
 *
 * @return {React.ReactNode} The plugin feature table row button slot.
 */
const PluginFeatureTableRowButtonSlot = ({
  language,
  translation,
  translationTableReducer
}) => {
  const {
    Slot
  } = createSlotFillWithId(language.slug);
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(Slot, {
    bubblesVirtually: true,
    as: "td",
    fillProps: {
      language,
      translation,
      translationTableReducer
    },
    className: "pll-feature-button-row-container"
  });
};

;// ./js/src/editors/common/hooks/use-authorized-languages.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Custom hook to get the memoized authorized languages.
 *
 * @return {Map} The memoized authorized languages.
 */
const useAuthorizedLanguages = () => {
  /**
   * @type {Map<string, Object>}
   */
  const languages = (0,external_this_wp_data_.useSelect)(select => select(MODULE_KEY).getLanguages());
  const authorizedLanguagesSlugs = pll_block_editor_plugin_settings?.authorizedLanguagesSlugs;
  return (0,external_this_wp_element_.useMemo)(() => {
    if (!authorizedLanguagesSlugs) {
      return languages;
    }
    return new Map(authorizedLanguagesSlugs.map(languageSlug => {
      const language = languages.get(languageSlug);
      if (language) {
        return [languageSlug, language];
      }
      return null;
    }).filter(Boolean));
  }, [languages, authorizedLanguagesSlugs]);
};
/* harmony default export */ const use_authorized_languages = (useAuthorizedLanguages);
;// ./js/src/editors/post/pro-features/index.js
/**
 * Registers Polylang Pro features for the metabox in the post editor.
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */






/**
 * Registers the Pro features for the post editor metabox.
 *
 * @return {void}
 */

const registerProFeatures = () => {
  (0,external_this_wp_plugins_.registerPlugin)('pll-machine-translation', {
    render: () => /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(plugin_feature_flag_button, {
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(machine_translation, {})
    })
  });
  (0,external_this_wp_plugins_.registerPlugin)('pll-duplicate-content', {
    render: () => /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(plugin_feature_flag_button, {
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(duplicate, {})
    })
  });
  const languages = (0,external_this_wp_data_.select)(MODULE_KEY).getLanguages();
  if (!languages || languages.length === 0) {
    return;
  }
  const SynchronizationFeatureButton = fillProps => {
    // Hook must run before the early return below.
    // eslint-disable-next-line @wordpress/no-unused-vars-before-return
    const authorizedLanguages = use_authorized_languages();
    if (!fillProps) {
      return null;
    }
    return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(buttons_synchronization, {
      post: fillProps.translation,
      language: fillProps.language,
      translationTableReducer: fillProps.translationTableReducer,
      disabled: !authorizedLanguages.has(fillProps.language.slug)
    });
  };
  languages.forEach(language => {
    (0,external_this_wp_plugins_.registerPlugin)(`pll-synchronization-${language.slug}`, {
      render: ({
        fillProps
      }) => /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(PluginFeatureTableRowButtonFill, {
        className: "pll-sync-column pll-column-icon",
        languageSlug: language.slug,
        children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(SynchronizationFeatureButton, {
          fillProps: fillProps
        })
      })
    });
  });
};
;// ./js/src/editors/common/components/display-notices/index.js
/**
 * WordPress Dependencies.
 */


const {
  stripTags
} = wp.sanitize;
const DisplayNotices = ({
  notices
}) => {
  const {
    createErrorNotice,
    createInfoNotice,
    createSuccessNotice,
    createWarningNotice
  } = (0,external_this_wp_data_.useDispatch)(external_this_wp_notices_.store);
  if (!notices) {
    return null;
  }
  notices.forEach(notice => {
    const noticeOptions = {
      type: 'snackbar',
      explicitDismiss: true
    };
    const message = stripTags(notice.message);
    switch (notice.type) {
      case 'error':
        createErrorNotice(message, noticeOptions);
        break;
      case 'info':
        createInfoNotice(message, noticeOptions);
        break;
      case 'success':
        createSuccessNotice(message, noticeOptions);
        break;
      case 'warning':
        createWarningNotice(message, noticeOptions);
        break;
    }
  });

  /**
   * Renderless component.
   */
  return null;
};
/* harmony default export */ const display_notices = (DisplayNotices);
;// ./js/src/editors/common/contexts/current-language/ref.js
/**
 * Module-level source of truth for the current Polylang language ("ref", not React useRef).
 * Readable outside React (REST middleware), subscribed to by useCurrentLanguage and
 * CurrentLanguageInvalidationProvider.
 */
/** @type {Object|null} */
let currentLanguage = null;

/** @type {Set<Function>} */
const listeners = new Set();

/**
 * @return {Object|null} Current Polylang language object for REST middleware.
 */
function getCurrentLanguageRef() {
  return currentLanguage;
}

/**
 * @param {Object|null} language Polylang language object.
 * @return {void}
 */
function setCurrentLanguageRef(language) {
  currentLanguage = language;
  listeners.forEach(listener => listener(language));
}

/**
 * @param {Function} listener Called when the current language changes.
 * @return {Function} Unsubscribe function.
 */
function subscribeCurrentLanguage(listener) {
  listeners.add(listener);
  return () => listeners.delete(listener);
}

/**
 * Seeds the ref from PHP bootstrap (call once before React mount).
 *
 * @return {void}
 */
function seedCurrentLanguageFromSettings() {
  if ('undefined' !== typeof pll_block_editor_plugin_settings && null !== pll_block_editor_plugin_settings.lang) {
    currentLanguage = pll_block_editor_plugin_settings.lang;
  }
}
// EXTERNAL MODULE: external {"this":["wp","url"]}
var external_this_wp_url_ = __webpack_require__(172);
;// ./js/src/editors/common/utils.js
/**
 * WordPress Dependencies
 */




/**
 * Escapes the RegExp special characters.
 *
 * @param {string} str Input string.
 * @return {string} Regex-escaped string.
 */
function escapeRegExp(str) {
  return str.replace(/[\\^$.*+?()[\]{}|]/g, '\\$&');
}

/**
 * Internal dependencies
 */
;


/**
 * Converts array of object to a map.
 *
 * @param {Array} array Array to convert.
 * @param {*}     key   The key in the object used as key to build the map.
 * @return {Map} Converted array.
 */
function convertArrayToMap(array, key) {
  const arrayMap = new Map();
  array.reduce((accumulator, currentValue) => {
    accumulator.set(currentValue[key], currentValue);
    return accumulator;
  }, arrayMap);
  return arrayMap;
}

/**
 * Converts map to an associative array.
 *
 * @param {Map} mapToConvert The map to convert.
 * @return {Object} Converted map.
 */
function convertMapToObject(mapToConvert) {
  const object = {};
  mapToConvert.forEach(function (value, key) {
    this[key] = typeof value === 'boolean' ? value.toString() : value;
  }, object);
  return object;
}

/**
 * Checks whether the current screen is the site editor.
 * Takes in account if Gutenberg is activated.
 *
 * @return {boolean} True if site editor screen, false otherwise.
 */
function isSiteBlockEditor() {
  return Boolean(document.getElementById('site-editor') || document.getElementById('edit-site-editor'));
}

/**
 * Returns the post type URL for REST API calls or undefined if the user hasn't the rights.
 *
 * @param {string} name The post type name.
 * @return {string|undefined} URL of the given post type, undefined if not available.
 */
function getPostsUrl(name) {
  const postTypes = (0,external_this_wp_data_.select)('core').getEntitiesConfig('postType');
  const postType = postTypes?.find(pt => pt.name === name);
  return postType?.baseURL;
}

/**
 * Gets all query string parameters and convert them in a URLSearchParams object.
 *
 * @return {URLSearchParams|null} Search parameters object, null if none.
 */
function getSearchParams() {
  // Variable window.location.search is just read for creating and returning a URLSearchParams object to be able to manipulate it more easily.
  if (window.location.search) {
    return new URLSearchParams(window.location.search);
  }
  return null;
}

/**
 * Gets selected language.
 *
 * @param {string} lang The post language code.
 * @return {Object|null} The selected language, null otherwise.
 */
function getSelectedLanguage(lang) {
  const languages = (0,external_this_wp_data_.select)(MODULE_KEY).getLanguages();
  // Pick up this language as selected in languages list
  if (languages) {
    return languages.get(lang);
  }
  return null;
}

/**
 * Gets the default language.
 *
 * @return {Object} The default Language.
 */
function getDefaultLanguage() {
  const languages = (0,external_this_wp_data_.select)(MODULE_KEY).getLanguages();
  return Array.from(languages.values()).find(lang => lang.is_default);
}

/**
 * Checks if the given language is the default one.
 *
 * @param {string} lang The language code to compare with.
 * @return {boolean} True if the given language is the default one.
 */
function isDefaultLanguage(lang) {
  return lang === getDefaultLanguage().slug;
}

/**
 * Checks if the given request is for saving.
 *
 * @param {Object} options The initial request.
 * @return {boolean} True if the request is for saving.
 */
function isSaveRequest(options) {
  // If data is defined we are in a PUT or POST request method otherwise a GET request method
  // Test options.method property isn't efficient because most of REST request which use fetch API doesn't pass this property.
  // So, test options.data is necessary to know if the REST request is to save data.
  // However test if options.data is undefined isn't sufficient because some REST request pass a null value as the ServerSideRender Gutenberg component.
  if (options.data) {
    return true;
  }
  return false;
}

/**
 * Checks if the given request concerns the current post type.
 *
 * Useful when saving a reusable block contained in another post type.
 * Indeed a reusable block is also a post, but its saving request doesn't concern the post currently edited.
 * As we don't know the language of the reusable block when the user triggers the reusable block saving action,
 * we need to pass the current post language to be sure that the reusable block will have a language.
 *
 * @see https://github.com/polylang/polylang/issues/437 - Reusable block has no language when it's saved from another post type editing.
 *
 * @param {Object} options the initial request
 * @return {boolean} True if the request concerns the current post.
 */
function isCurrentPostRequest(options) {
  // Saving translation data is needed only for all post types.
  // It's done by verifying options.path matches with one of baseURL of all post types
  // and compare current post id with this sent in the request.

  // List of post type baseURLs.
  const postTypeURLs = (0,external_this_wp_data_.select)('core').getEntitiesConfig('postType')?.map(pt => pt.baseURL) || [];

  // Id from the post currently edited.
  const postId = (0,external_this_wp_data_.select)('core/editor').getCurrentPostId();

  // Id from the REST request.
  // options.data never null/undefined here because it's already verified before in isSaveRequest() function.
  const id = options.data.id;

  // Return true
  // if REST request baseURL matches with one of the known post type baseURLs
  // and the id from the post currently edited corresponds on the id passed to the REST request
  // Return false otherwise
  return -1 !== postTypeURLs.findIndex(element => {
    return new RegExp(`${escapeRegExp(element)}`).test(options.path);
  }) && postId === id;
}

/**
 * Checks if the given REST request is for the creation of a new template part translation.
 *
 * @param {Object} options The initial request.
 * @return {boolean} True if the request concerns a template part translation creation.
 */
function isTemplatePartTranslationCreationRequest(options) {
  return 'POST' === options.method && options.path.match(/^\/wp\/v2\/template-parts(?:\/|\?|$)/) && Boolean(options.data.from_post) && Boolean(options.data.lang);
}

/**
 * Checks if the given REST request is for the creation of a new template part.
 *
 * @param {Object} options The initial request.
 * @return {boolean} True if the request concerns a template part creation.
 */
function isNewTemplatePartCreationRequest(options) {
  return 'POST' === options.method && options.path.match(/^\/wp\/v2\/template-parts(?:\/|\?|$)/) && !options.data.from_post && !options.data.lang;
}

/**
 * Adds language as query string parameter to the given request.
 *
 * @param {Object} options         The initial request.
 * @param {string} currentLanguage The language code to add to the request.
 */
function addLanguageToRequest(options, currentLanguage) {
  const hasLangArg = (0,external_this_wp_url_.hasQueryArg)(options.path, 'lang');
  if ((options.filterLang === undefined || options.filterLang) && !hasLangArg) {
    options.path = (0,external_this_wp_url_.addQueryArgs)(options.path, {
      lang: currentLanguage
    });
  }
}

/**
 * Adds `include_untranslated` parameter to the request.
 *
 * @param {Object} options The initial request.
 * @return {void}
 */
function addIncludeUntranslatedParam(options) {
  options.path = (0,external_this_wp_url_.addQueryArgs)(options.path, {
    include_untranslated: true
  });
}

/**
 * Use addIncludeUntranslatedParam if the given page is a template part page.
 * Or if the template editing mode is enabled inside post editing.
 *
 * @param {Object} options The initial request.
 * @return {void}
 */
function maybeRequireIncludeUntranslatedTemplate(options) {
  const params = new URL(document.location).searchParams;
  const postType = params.get('postType');
  const postId = params.get('postId');
  const isEditingTemplate = (0,external_this_wp_data_.select)(MODULE_POST_EDITOR_KEY)?.isEditingTemplate();
  if ('wp_template_part' === postType && postId || isEditingTemplate) {
    addIncludeUntranslatedParam(options);
  }
}

/**
 * Returns true if the given post is a template part, false otherwise.
 *
 * @param {Object} post A post object.
 * @return {boolean} Whether it is a template part or not.
 */
function isTemplatePart(post) {
  return 'wp_template_part' === post.type;
}

/**
 * Returns the current post type considering the Site Editor or Post Editor.
 *
 * @return {string} The current post type.
 */
function getCurrentPostType() {
  if (isSiteBlockEditor()) {
    return (0,external_this_wp_data_.select)(MODULE_SITE_EDITOR_KEY).getEditedPostType();
  }
  return (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getCurrentPostType();
}

/**
 * Adds parameters according to the context of the request.
 *
 * @since 3.5
 *
 * @param {APIFetchOptions} options The options of the request.
 * @return {APIFetchOptions} The modified options of the request.
 */
function addParametersToRequest(options) {
  const currentLangSlug = getCurrentLanguageRef()?.slug ?? pll_block_editor_plugin_settings.lang.slug;

  // `POST` or `PUT` request.
  if (isSaveRequest(options)) {
    /**
     * Use default language for new template part that doesn't exist in any language,
     * otherwise use the current language.
     */
    if (isNewTemplatePartCreationRequest(options)) {
      addLanguageToRequest(options, getDefaultLanguage()?.slug);
    }
    if (!isCurrentPostRequest(options) && !isTemplatePartTranslationCreationRequest(options)) {
      addLanguageToRequest(options, currentLangSlug);
    }
    maybeAddLangSuffixToTemplatePart(options, currentLangSlug);
    return options;
  }
  const currentPostType = getCurrentPostType();

  // Current language is set to default when editing templates.
  if ('wp_template' === currentPostType) {
    addLanguageToRequest(options, getDefaultLanguage()?.slug);
  }
  const templatePartListRegex = new RegExp(/^\/wp\/v2\/template-parts\/?(?:\?.*)?$/);

  // Template part list request.
  if (templatePartListRegex.test(options.path)) {
    maybeRequireIncludeUntranslatedTemplate(options);
  }

  // All kinds of requests.
  addLanguageToRequest(options, currentLangSlug);
  return options;
}

/**
 * Adds the language suffix to a template part only during creation.
 *
 * @param {Object} options  Object representing a REST request.
 * @param {string} langSlug The Language slug to add.
 * @return {void}
 */
function maybeAddLangSuffixToTemplatePart(options, langSlug) {
  const restBaseUrl = getPostsUrl('wp_template_part');
  if (!restBaseUrl) {
    // The user hasn't the rights to edit template part.
    return;
  }
  const templatePartURLRegExp = new RegExp(escapeRegExp(restBaseUrl));
  if ('POST' === options.method && templatePartURLRegExp.test(options.path)) {
    const languages = (0,external_this_wp_data_.select)(MODULE_KEY).getLanguages();
    const language = languages.get(langSlug);
    if (!language.is_default) {
      // No suffix for default language.
      const langSuffix = (/* inlined export .TEMPLATE_PART_SLUG_SEPARATOR */"___") + langSlug;
      options.data.slug += langSuffix;
    }
  }
}

/**
 * Invalidates cached Core Data entity resolutions.
 *
 * @return {void}
 */
function invalidateResolutionForStoreSelectors() {
  const {
    invalidateResolutionForStoreSelector
  } = (0,external_this_wp_data_.dispatch)(external_this_wp_coreData_.store);
  ['getEntityRecord', 'getEntityRecords', 'getMedia'].forEach(invalidateResolutionForStoreSelector);
}
;// ./js/src/editors/common/contexts/current-language/provider.js
/* unused harmony import specifier */ var useState;
/* unused harmony import specifier */ var useRef;
/* unused harmony import specifier */ var useEffect;
/* unused harmony import specifier */ var provider_getCurrentLanguageRef;
/* unused harmony import specifier */ var provider_subscribeCurrentLanguage;
/* unused harmony import specifier */ var provider_invalidateResolutionForStoreSelectors;
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



/**
 * Updates current language for REST and React subscribers.
 *
 * @param {Object|string|null|undefined} languageOrSlug Polylang language object or slug.
 * @return {void}
 */
function setCurrentLanguage(languageOrSlug) {
  let language = null;
  if ('string' === typeof languageOrSlug) {
    language = getSelectedLanguage(languageOrSlug);
  } else {
    language = languageOrSlug;
  }
  if (!language) {
    return;
  }
  setCurrentLanguageRef(language);
}

/**
 * Subscribes to the module-level current-language ref (works outside React Context,
 * e.g. when PluginSidebar renders via SlotFill).
 *
 * @return {{ language: Object|null, setCurrentLanguage: Function }} Current language API.
 */
function useCurrentLanguage() {
  const language = (0,external_this_wp_element_.useSyncExternalStore)(subscribeCurrentLanguage, getCurrentLanguageRef, getCurrentLanguageRef);
  return {
    language,
    setCurrentLanguage
  };
}

/**
 * Invalidates core-data when the current language slug changes (Site Editor).
 *
 * @param {Object}   props                         Component props.
 * @param {boolean}  props.invalidateOnLanguageChange Whether to invalidate core-data when slug changes.
 * @param {import('@wordpress/element').ReactNode} props.children Children.
 * @return {import('@wordpress/element').ReactNode} Children.
 */
function CurrentLanguageInvalidationProvider({
  invalidateOnLanguageChange = false,
  children
}) {
  const [language, setLanguageState] = useState(() => provider_getCurrentLanguageRef());
  const skipInvalidationRef = useRef(true);
  const previousSlugRef = useRef(language?.slug);
  useEffect(() => provider_subscribeCurrentLanguage(setLanguageState), []);
  useEffect(() => {
    if (!invalidateOnLanguageChange) {
      return;
    }
    if (skipInvalidationRef.current) {
      skipInvalidationRef.current = false;
      previousSlugRef.current = language?.slug;
      return;
    }
    if (previousSlugRef.current !== language?.slug) {
      previousSlugRef.current = language?.slug;
      provider_invalidateResolutionForStoreSelectors();
    }
  }, [language?.slug, invalidateOnLanguageChange]);
  return children;
}
;// ./js/src/editors/common/contexts/current-language/rest-bridge.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



/**
 * Keeps the current-language ref aligned with the block editor and Site Editor canvas.
 *
 * @return {null} Renderless component.
 */
const CurrentLanguageRestBridge = () => {
  const editedLang = (0,external_this_wp_data_.useSelect)(select => select(external_this_wp_editor_.store).getEditedPostAttribute('lang'), []);
  const editedPostId = (0,external_this_wp_data_.useSelect)(select => select(external_this_wp_editor_.store).getCurrentPostId(), []);
  (0,external_this_wp_element_.useEffect)(() => {
    const params = new URL(document.location.href).searchParams;
    const postType = params.get('postType');
    const postId = params.get('postId');
    if ('wp_template_part' === postType && !postId) {
      setCurrentLanguage(getDefaultLanguage());
      return;
    }
    if (isSiteBlockEditor()) {
      return;
    }
    if (editedLang) {
      const language = getSelectedLanguage(editedLang);
      if (language) {
        setCurrentLanguage(language);
      }
    }
  }, [editedLang, editedPostId]);
  return null;
};
/* harmony default export */ const rest_bridge = (CurrentLanguageRestBridge);
;// ./js/src/editors/common/store/utils.js
/* unused harmony import specifier */ var subscribe;
/* unused harmony import specifier */ var utils_select;
/* unused harmony import specifier */ var utils_MODULE_SITE_EDITOR_KEY;
/* unused harmony import specifier */ var utils_MODULE_CORE_EDITOR_KEY;
/**
 * WordPress Dependencies
 */


/**
 * Internal dependencies
 */



/**
 * Wait for the whole post block editor context has been initialized: current post loaded and languages list initialized.
 */
const isBlockPostEditorContextInitialized = () => {
  if (!(0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY)) {
    return Promise.reject("Polylang languages panel can't be initialized because block editor isn't fully initialized.");
  }

  // save url params especially when a new translation is creating
  saveURLParams();

  /**
   * Set a promise fulfilled with the current post.
   */
  const isCurrentPostLoaded = new Promise(resolve => {
    const unsubscribe = (0,external_this_wp_data_.subscribe)(() => {
      const currentPost = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).getCurrentPost();
      if (currentPost && Object.keys(currentPost).length > 0) {
        unsubscribe();
        resolve(currentPost);
      }
    });
  });

  /**
   * Set a promise fulfilled with the source post when a new draft is created from a source, null otherwise.
   */
  const isFromPostLoaded = new Promise(resolve => {
    const unsubscribe = (0,external_this_wp_data_.subscribe)(() => {
      const fromPostUrlParams = (0,external_this_wp_data_.select)(MODULE_KEY).getFromPost();
      if (!fromPostUrlParams || !fromPostUrlParams.id || !fromPostUrlParams.postType) {
        unsubscribe();
        resolve(null);
        return;
      }
      const fromPost = (0,external_this_wp_data_.select)((/* inlined export .MODULE_CORE_KEY */"core")).getEntityRecord('postType', fromPostUrlParams.postType, fromPostUrlParams.id, {
        context: 'view'
      } // Use 'view' context so translators can read posts they cannot edit.
      );
      if (fromPost && fromPost.id) {
        unsubscribe();
        resolve(fromPost);
      }
    });
  });
  return Promise.all([isCurrentPostLoaded, isFromPostLoaded, isLanguagesinitialized()]).then(resolvedValues => {
    const [currentPost, fromPost] = resolvedValues;

    // Force update translations when creating a draft from a source post.
    if (fromPost && fromPost.id && currentPost.lang) {
      (0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).editPost({
        translations: {
          ...fromPost.translations,
          [currentPost.lang]: currentPost.id
        }
      });
    }
  });
};

/**
 * Wait for the whole site editor context to be initialized: current template loaded and languages list initialized.
 */
const isSiteEditorContextInitialized = () => {
  // save url params especially when a new translation is creating
  saveURLParams();

  /**
   * Set a promise to wait for the current template to be fully loaded before making other processes.
   * It allows to see if both Site Editor and Core stores are available (@see getCurrentPostFromDataStore()).
   */
  const isTemplatePartLoaded = new Promise(resolve => {
    const unsubscribe = subscribe(() => {
      const store = utils_select(utils_MODULE_SITE_EDITOR_KEY);
      if (store) {
        unsubscribe();
        resolve();
      }
    });
  });
  return Promise.all([isTemplatePartLoaded, isLanguagesinitialized()]);
};

/**
 * Returns a promise fulfilled when the languages list is correctly initialized before making other processes.
 *
 * @return {Promise} A promise fulfilled with the languages list.
 */
const isLanguagesinitialized = () => new Promise(resolve => {
  const unsubscribe = (0,external_this_wp_data_.subscribe)(() => {
    const languages = (0,external_this_wp_data_.select)(MODULE_KEY)?.getLanguages();
    if (languages?.size > 0) {
      unsubscribe();
      resolve(languages);
    }
  });
});

/**
 * Save query string parameters from URL. They could be needed after
 * They could be null if they does not exist
 */
function saveURLParams() {
  // Variable window.location.search isn't use directly
  // Function getSearchParams return an URLSearchParams object for manipulating each parameter
  // Each of them are sanitized below
  const searchParams = getSearchParams();
  const fromPost = searchParams && searchParams.get('from_post');
  const postType = searchParams && searchParams.get('post_type');
  const newLanguage = searchParams && searchParams.get('new_lang');
  if (fromPost && postType && newLanguage) {
    (0,external_this_wp_data_.dispatch)(MODULE_KEY).setFromPost({
      id: wp.sanitize.stripTagsAndEncodeText(fromPost),
      postType: wp.sanitize.stripTagsAndEncodeText(postType),
      newLanguage: wp.sanitize.stripTagsAndEncodeText(newLanguage)
    });
  }
}

/**
 * Gets the current post using the editor stores and the Core store.
 *
 * Prefers `core/editor` selectors (recommended since WordPress 6.8) and falls
 * back to the deprecated `core/edit-site` selectors for older environments.
 *
 * @return {object|null} The current post object, `null` if none found.
 */
const getCurrentPostFromDataStore = () => {
  const editorSelector = utils_select(utils_MODULE_CORE_EDITOR_KEY);
  const postId = editorSelector?.getCurrentPostId?.();
  const postType = editorSelector?.getCurrentPostType?.();
  if (postId && postType) {
    return utils_select((/* inlined export .MODULE_CORE_KEY */"core")).getEntityRecord('postType', postType, postId);
  }
  const siteEditorSelector = utils_select(utils_MODULE_SITE_EDITOR_KEY);

  /**
   * Return null when called from our apiFetch middleware without a properly loaded store.
   */
  if (!siteEditorSelector) {
    return null;
  }
  const context = siteEditorSelector.getEditedPostContext();
  const editedContext = context?.postType && context?.postId ? context : {
    postId: siteEditorSelector.getEditedPostId(),
    postType: siteEditorSelector.getEditedPostType()
  };
  return null === editedContext ? null : utils_select((/* inlined export .MODULE_CORE_KEY */"core")).getEntityRecord('postType', editedContext.postType, editedContext.postId);
};
;// ./js/src/editors/common/contexts/current-language/history-sync.js
/* unused harmony import specifier */ var history_sync_useEffect;
/* unused harmony import specifier */ var history_sync_setCurrentLanguage;
/* unused harmony import specifier */ var history_sync_UNTRANSLATABLE_POST_TYPE;
/* unused harmony import specifier */ var history_sync_getCurrentPostFromDataStore;
/* unused harmony import specifier */ var history_sync_getSelectedLanguage;
/* unused harmony import specifier */ var history_sync_getDefaultLanguage;
/* unused harmony import specifier */ var history_sync_invalidateResolutionForStoreSelectors;
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */





/**
 * @param {string} value Encoded URI component.
 * @return {string} Decoded value, or the input when decoding fails.
 */
const safeDecodeURIComponent = value => {
  try {
    return decodeURIComponent(value);
  } catch {
    return value;
  }
};

/**
 * Hub / list Site Editor routes where REST should use the default language.
 * Mirrors {@link PLL_FSE_Language::get_current_post()} when no post is resolved.
 *
 * @param {string} path Decoded `p` path (leading slash).
 * @return {boolean} Whether the path is a generic screen.
 */
const isGenericSiteEditorPath = path => {
  const genericPaths = ['', '/', '/navigation', '/page', '/pattern', '/patterns', '/styles', '/template', '/templates', '/wp_template_part/all'];
  return genericPaths.includes(path);
};

/**
 * @param {URLSearchParams} params Location search parameters.
 * @return {string|null} Stable route identity for the edited entity.
 */
const getRouteIdentity = params => {
  const routePath = params.get('p');
  if (routePath) {
    const decoded = safeDecodeURIComponent(routePath);
    return decoded.replace(/^\/([^/]+)\/(.+)$/, `$1:$2`);
  }
  const postType = params.get('postType');
  const postId = params.get('postId');
  if (postType && postId) {
    return `${postType}:${postId}`;
  }
  return null;
};

/**
 * @param {URLSearchParams} params   Next location search parameters.
 * @param {string|null}     identity Route identity from {@link getRouteIdentity}.
 * @return {boolean} Whether the default language should apply.
 */
const shouldUseDefaultLanguage = (params, identity) => {
  if (!identity) {
    return true;
  }
  const routePath = params.get('p');
  const decodedPath = routePath ? safeDecodeURIComponent(routePath) : null;
  if (decodedPath && isGenericSiteEditorPath(decodedPath)) {
    return true;
  }
  if (decodedPath && decodedPath.includes('/wp_global_styles')) {
    return true;
  }
  if (typeof identity === 'string' && identity.startsWith('/') && isGenericSiteEditorPath(identity)) {
    return true;
  }
  const postType = identity.includes(':') ? identity.split(':')[0] : null;
  if (postType && history_sync_UNTRANSLATABLE_POST_TYPE.includes(postType)) {
    return true;
  }
  if (postType === 'wp_template_part' && identity.endsWith(':all')) {
    return true;
  }
  return false;
};

/**
 * Reacts to Site Editor history navigation and updates current language / core-data cache.
 *
 * @param {Object}   props            Component props.
 * @param {Function} props.onPromise  Resolves when the Site Editor context is ready.
 * @return {null} Renderless component.
 */
const CurrentLanguageHistorySync = ({
  onPromise
}) => {
  history_sync_useEffect(() => {
    const getLanguageFromCanvas = () => {
      const currentPost = history_sync_getCurrentPostFromDataStore();
      return history_sync_getSelectedLanguage(currentPost?.lang);
    };
    let lastKnownHref = document.location.href;
    const applyLocationChange = nextLocation => {
      const resolvedNextHref = new URL(nextLocation, lastKnownHref).href;
      const currentParams = new URL(lastKnownHref).searchParams;
      const nextParams = new URL(resolvedNextHref).searchParams;
      const currentIdentity = getRouteIdentity(currentParams);
      const nextIdentity = getRouteIdentity(nextParams);
      if (currentIdentity === nextIdentity) {
        lastKnownHref = resolvedNextHref;
        return;
      }
      if (shouldUseDefaultLanguage(nextParams, nextIdentity)) {
        history_sync_setCurrentLanguage(history_sync_getDefaultLanguage());
      }
      history_sync_invalidateResolutionForStoreSelectors();
      lastKnownHref = resolvedNextHref;
    };
    const onPopState = () => {
      applyLocationChange(document.location.href);
    };
    onPromise().then(() => {
      const language = getLanguageFromCanvas();
      if (language) {
        history_sync_setCurrentLanguage(language);
      }
    });
    const {
      pushState,
      replaceState
    } = window.history;
    const originalPushState = pushState.bind(window.history);
    const originalReplaceState = replaceState.bind(window.history);
    window.history.pushState = (state, key, path) => {
      applyLocationChange(path);
      return originalPushState(state, key, path);
    };
    window.history.replaceState = (state, key, path) => {
      applyLocationChange(path);
      return originalReplaceState(state, key, path);
    };
    window.addEventListener('popstate', onPopState);
    return () => {
      window.history.pushState = pushState;
      window.history.replaceState = replaceState;
      window.removeEventListener('popstate', onPopState);
    };
  }, [onPromise]);
  return null;
};
/* harmony default export */ const history_sync = ((/* unused pure expression or super */ null && (CurrentLanguageHistorySync)));
;// ./js/src/editors/common/contexts/current-language/index.js




;// ./js/src/editors/common/components/inline-translation-icon/index.js
/**
 * External dependencies.
 */


/**
 * Displays a translation icon.
 *
 * @since 3.9
 *
 * @return {React.ReactElement} Icon component.
 */

function InlineTranslationIcon() {
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("span", {
    className: "pll-translation-icon",
    children: icons_translation
  });
}
/* harmony default export */ const inline_translation_icon = (InlineTranslationIcon);
;// ./js/src/editors/common/components/language-flag-aria-hidden/index.js


/**
 * Displays a flag icon for a given language, but hidden from screen readers.
 * This is useful if the flag is displayed next to a text that would make it redundant (like a language name).
 *
 * @since 3.9
 *
 * @param {Object} props          LanguageFlag props.
 * @param {Object} props.language Language object for the flag.
 *
 * @return {React.ReactElement} Flag component.
 */

function LanguageFlagAriaHidden({
  language
}) {
  if (!language) {
    return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(inline_translation_icon, {});
  }
  if (language.flag_url) {
    return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("span", {
      className: "pll-select-flag",
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("img", {
        src: language.flag_url,
        alt: language.name,
        "aria-hidden": "true",
        tabIndex: "-1",
        className: "flag"
      })
    });
  }
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("abbr", {
    "aria-hidden": "true",
    tabIndex: "-1",
    children: language.slug?.toUpperCase()
  });
}
/* harmony default export */ const language_flag_aria_hidden = (LanguageFlagAriaHidden);
;// ./js/src/editors/common/components/language-dropdown/index.js
/**
 * WordPress dependencies.
 */



/**
 * Internal dependencies.
 */


/**
 * Displays a dropdown to select a language.
 *
 * @since 3.1
 *
 * @param {Object}   props                  LanguageDropdown props.
 * @param {Function} props.handleChange     Callback to be executed when language changes.
 * @param {Object}   props.languages        An iterable object containing languages objects.
 * @param {Object}   props.selectedLanguage An object representing a Polylang Language. Default to null.
 * @param {string}   props.defaultValue     Value to be selected if the selected language is not provided. Default to an empty string.
 *
 * @return {Object} A dropdown selector for languages.
 */

function LanguageDropdown({
  handleChange,
  languages,
  selectedLanguage = null,
  defaultValue = ''
}) {
  const selectedLanguageSlug = selectedLanguage?.slug ? selectedLanguage.slug : defaultValue;
  const normalizedLanguagesForSelectControl = (0,external_this_wp_element_.useMemo)(() => {
    return Array.from(languages.values()).map(({
      slug,
      name
    }) => ({
      value: slug,
      label: name
    }));
  }, [languages]);
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)("div", {
    id: "pll-language-select-control",
    children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(language_flag_aria_hidden, {
      language: selectedLanguage
    }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.SelectControl, {
      value: selectedLanguageSlug,
      onChange: newLangSlug => handleChange(newLangSlug),
      options: normalizedLanguagesForSelectControl,
      id: "pll_post_lang_choice",
      name: "pll_post_lang_choice",
      className: "post_lang_choice",
      __nextHasNoMarginBottom: true,
      __next40pxDefaultSize: true
    })]
  });
}

;// ./js/src/editors/common/components/switcher/utils.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



/**
 * Persists language change.
 *
 * @param {Object} newLanguage         New language.
 * @param {Object} newTranslationsData New translations data.
 * @return {Promise} Save post promise.
 */
const saveLanguageChange = (newLanguage, newTranslationsData) => {
  return Promise.allSettled([(0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).editPost(newTranslationsData), (0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).savePost(),
  // Need to save post to recalculate permalink.
  forceLanguageSave(newLanguage.slug)]).then(() => {
    invalidateResolutionForStoreSelectors();
  });
};

/**
 * Tells whether the edited post is empty.
 *
 * @return {boolean} True if the edited post is empty, false otherwise.
 */
const isEditedPostEmpty = () => {
  const editor = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY);
  return !editor.getEditedPostAttribute('title') && !editor.getEditedPostContent() && !editor.getEditedPostAttribute('excerpt');
};

/**
 * Forces the save of the post in the new language even if no content has been written.
 * So the post metadata are correctly updated (e.g.: Attachable Medias).
 *
 * @since 3.0
 *
 * @param {string} lang A language slug.
 * @return {Promise} Save post promise.
 */
const forceLanguageSave = lang => {
  const editor = (0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY);
  if (isEditedPostEmpty()) {
    return external_this_wp_apiFetch_default()({
      path: (0,external_this_wp_url_.addQueryArgs)(`wp/v2/posts/${editor.getCurrentPostId()}`, {
        lang
      }),
      method: 'POST'
    });
  }
  return Promise.reject('Force save not required.');
};
;// ./js/src/editors/common/components/switcher/index.js
/**
 * Internal dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */




const Switcher = ({
  currentPost
}) => {
  const [isOpen, setOpen] = (0,external_this_wp_element_.useState)(false);
  const [selectedLang, setSelectedLang] = (0,external_this_wp_element_.useState)(null);
  const {
    createSuccessNotice,
    createErrorNotice
  } = (0,external_this_wp_data_.useDispatch)(external_this_wp_notices_.store);
  const {
    language: currentLanguage
  } = useCurrentLanguage();
  const languages = use_authorized_languages();
  (0,external_this_wp_element_.useEffect)(() => {
    if (currentLanguage) {
      setSelectedLang(currentLanguage);
    }
  }, [currentLanguage]);
  const prevLangRef = (0,external_this_wp_element_.useRef)(currentLanguage);
  (0,external_this_wp_element_.useEffect)(() => {
    prevLangRef.current = currentLanguage;
  }, [currentLanguage]);
  const openModal = newLangSlug => {
    const newLang = languages.get(newLangSlug);
    setSelectedLang(newLang);
    if (isEditedPostEmpty()) {
      confirmChange(newLang);
      return;
    }
    setOpen(true);
  };
  const closeModal = () => setOpen(false);
  const confirmChange = _selectedLang => {
    closeModal();
    if (!_selectedLang) {
      createErrorNotice((0,external_this_wp_i18n_.__)('Failed to save selected language', 'polylang-pro'), {
        type: 'snackbar'
      });
      return;
    }
    const newTranslations = {
      ...currentPost.translations
    };
    delete newTranslations[prevLangRef.current.slug];

    // Only add the new language if it is not already assigned.
    if (!Object.prototype.hasOwnProperty.call(newTranslations, selectedLang.slug)) {
      newTranslations[selectedLang.slug] = currentPost.id;
    }
    const synchronizedPosts = {
      ...currentPost.synchronizedPosts
    };
    delete synchronizedPosts[prevLangRef.current.slug];
    saveLanguageChange(_selectedLang, {
      lang: _selectedLang.slug,
      translations: newTranslations,
      pll_sync_post: synchronizedPosts
    }).then(() => {
      createSuccessNotice((0,external_this_wp_i18n_.__)('Language changed', 'polylang-pro'), {
        type: 'snackbar'
      });
      document.dispatchEvent(new CustomEvent('onPostLangChoice', {
        detail: {
          lang: _selectedLang
        }
      }));
    });
  };
  const abortChange = () => {
    setSelectedLang(prevLangRef.current);
    closeModal();
  };
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)((react_jsx_runtime_production_min_namespaceFn().Fragment), {
    children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("p", {
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("strong", {
        children: (0,external_this_wp_i18n_.__)('Language', 'polylang-pro')
      })
    }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("label", {
      className: "screen-reader-text",
      htmlFor: "pll_post_lang_choice",
      children: (0,external_this_wp_i18n_.__)('Language', 'polylang-pro')
    }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(LanguageDropdown, {
      selectedLanguage: selectedLang,
      handleChange: openModal,
      languages: languages
    }), isOpen && /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)(external_this_wp_components_.Modal, {
      title: (0,external_this_wp_i18n_.__)('Change language', 'polylang-pro'),
      onRequestClose: abortChange,
      children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("p", {
        children: (0,external_this_wp_i18n_.__)('Are you sure you want to change the language of the current content?', 'polylang-pro')
      }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)("div", {
        role: "group",
        className: "components-button-group buttons",
        children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.Button, {
          variant: "tertiary",
          onClick: abortChange,
          type: "button",
          children: (0,external_this_wp_i18n_.__)('Cancel', 'polylang-pro')
        }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("span", {
          children: "\xA0"
        }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.Button, {
          variant: "primary",
          onClick: () => confirmChange(selectedLang),
          type: "submit",
          children: (0,external_this_wp_i18n_.__)('Change', 'polylang-pro')
        })]
      })]
    })]
  });
};
/* harmony default export */ const switcher = (Switcher);
;// ./js/src/editors/common/components/translations-table/cells/add-or-edit/index.js

const AddOrEditCell = ({
  children
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("td", {
    className: "pll-edit-column pll-column-icon",
    children: children
  });
};
/* harmony default export */ const add_or_edit = (AddOrEditCell);
;// ./js/src/editors/common/components/default-lang-icon/index.js
/* unused harmony import specifier */ var Icon;
/* unused harmony import specifier */ var default_lang_icon_;
/* unused harmony import specifier */ var star;
/* unused harmony import specifier */ var _jsxs;
/* unused harmony import specifier */ var _Fragment;
/* unused harmony import specifier */ var default_lang_icon_jsx;
/**
 * WordPress dependencies
 */



/**
 * External dependencies
 */


const DefaultLangIcon = () => /*#__PURE__*/_jsxs(_Fragment, {
  children: [/*#__PURE__*/default_lang_icon_jsx(Icon, {
    icon: star,
    className: "pll-default-lang-icon"
  }), /*#__PURE__*/default_lang_icon_jsx("span", {
    className: "screen-reader-text",
    children: default_lang_icon_('Default language.', 'polylang-pro')
  })]
});
/* harmony default export */ const default_lang_icon = ((/* unused pure expression or super */ null && (DefaultLangIcon)));
;// ./js/src/editors/common/components/translations-table/cells/default-language/index.js
/* unused harmony import specifier */ var default_language_DefaultLangIcon;
/* unused harmony import specifier */ var default_language_jsx;
/**
 * Internal dependencies
 */


const DefaultLanguageCell = ({
  isDefault
}) => {
  return /*#__PURE__*/default_language_jsx("td", {
    className: "pll-default-lang-column pll-column-icon",
    children: isDefault && /*#__PURE__*/default_language_jsx(default_language_DefaultLangIcon, {})
  });
};
/* harmony default export */ const default_language = ((/* unused pure expression or super */ null && (DefaultLanguageCell)));
;// ./js/src/editors/common/components/translations-table/cells/delete/index.js
/* unused harmony import specifier */ var delete_jsx;

const DeleteCell = ({
  children
}) => {
  return /*#__PURE__*/delete_jsx("td", {
    className: "pll-delete-column pll-column-icon",
    children: children
  });
};
/* harmony default export */ const cells_delete = ((/* unused pure expression or super */ null && (DeleteCell)));
;// ./js/src/editors/common/components/language-flag/index.js


/**
 * Displays a flag icon for a given language.
 *
 * @since 3.1
 * @since 3.2 Now its own component.
 *
 * @param {Object} props          LanguageFlag props.
 * @param {Object} props.language Language object for the flag.
 *
 * @return {React.ReactElement} Flag component.
 */

function LanguageFlag({
  language
}) {
  if (!language) {
    return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(inline_translation_icon, {});
  }
  if (language.flag_url) {
    return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("span", {
      className: "pll-select-flag",
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("img", {
        src: language.flag_url,
        alt: language.name,
        lang: language.w3c,
        dir: language.is_rtl ? 'rtl' : 'ltr',
        className: "flag"
      })
    });
  }
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("abbr", {
    title: language.name,
    lang: language.w3c,
    dir: language.is_rtl ? 'rtl' : 'ltr',
    children: language.slug?.toUpperCase()
  });
}
/* harmony default export */ const language_flag = (LanguageFlag);
;// ./js/src/editors/common/components/translations-table/cells/flag/index.js
/**
 * Internal dependencies
 */


const FlagCell = ({
  language
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("th", {
    className: "pll-language-column",
    children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(language_flag, {
      language: language
    })
  });
};
/* harmony default export */ const flag = (FlagCell);
;// ./js/src/editors/common/components/translations-table/cells/translation-input/index.js

const TranslationInputCell = ({
  children
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("td", {
    className: "pll-translation-column",
    children: children
  });
};
/* harmony default export */ const translation_input = (TranslationInputCell);
;// ./js/src/editors/common/components/translations-table/cells/index.js
/**
 * Cells components for translations table.
 */






// EXTERNAL MODULE: external {"this":["wp","htmlEntities"]}
var external_this_wp_htmlEntities_ = __webpack_require__(989);
;// ./js/src/editors/common/components/translations-table/input/index.js
/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */


/**
 * Translation input component.
 *
 * @param {Object} props                   The component props.
 * @param {Object} props.language          The language object.
 * @param {Object} props.source            The source post object.
 * @param {Object} props.translation       The translation post object.
 * @param {Object} props.tableDispatch     The table dispatch function.
 * @param {Map}    props.translationsTable The translations table object.
 * @return {React.Component} The translation input component.
 */

const TranslationInput = ({
  language,
  source,
  translation,
  tableDispatch,
  translationsTable
}) => {
  const [options, setOptions] = (0,external_this_wp_element_.useState)(translation ? [{
    value: translation.id,
    label: getPostTitle(translation)
  }] : []);
  const [value, setValue] = (0,external_this_wp_element_.useState)(translation ? translation.id : null);
  const [isLoading, setIsLoading] = (0,external_this_wp_element_.useState)(false);
  (0,external_this_wp_element_.useEffect)(() => {
    if (translation && options.length === 0) {
      setOptions([{
        value: translation.id,
        label: getPostTitle(translation)
      }]);
    }
  }, [translation, options]);
  (0,external_this_wp_element_.useEffect)(() => {
    if (translation) {
      if (options.find(option => option.value === translation.id && option.label !== getPostTitle(translation) || option.value !== translation.id && 0 === option.value && option.label === getPostTitle(translation))) {
        // Set options for optimistically rendered translation.
        setOptions([{
          value: translation.id,
          label: getPostTitle(translation)
        }]);
      }
      setValue(translation.id);
    } else {
      setValue(null);
    }
  }, [translation, options]);
  const onInputChange = nextValue => {
    setValue(nextValue);
    if ((0,external_this_wp_data_.select)(MODULE_CORE_EDITOR_KEY).isEditedPostNew()) {
      (0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).savePost();
    }
    if (!nextValue) {
      tableDispatch({
        type: 'remove_translation',
        lang: language
      });
      const newTranslations = {
        ...transformTranslationsTableToObject(translationsTable)
      };
      delete newTranslations[language.slug];
      (0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).editPost({
        translations: newTranslations
      });
      return;
    }
    const postToRender = getPostToRender(options, nextValue);
    if (!postToRender) {
      return;
    }
    tableDispatch({
      type: 'add_translation',
      lang: language,
      post: postToRender // Optimistically rendered post.
    });
    (0,external_this_wp_data_.dispatch)(MODULE_CORE_EDITOR_KEY).editPost({
      translations: {
        ...transformTranslationsTableToObject(translationsTable),
        [language.slug]: parseInt(nextValue, 10)
      }
    });
  };
  const fetchUntranslatedPosts = (0,external_this_wp_element_.useCallback)(search => {
    if ('' === search) {
      return [];
    }
    setIsLoading(true);
    external_this_wp_apiFetch_default()({
      path: (0,external_this_wp_url_.addQueryArgs)('/pll/v1/untranslated-posts', {
        search,
        include: source.id,
        untranslated_in: source.lang,
        lang: language.slug,
        type: source.type,
        context: 'edit'
      })
    }).then(posts => {
      setOptions(posts.map(post => {
        return {
          value: parseInt(post.id, 10),
          label: getPostTitle(post)
        };
      }));
    }).finally(() => {
      setIsLoading(false);
    });
  }, [source, language, setOptions]);
  const debounce = (0,external_this_wp_element_.useCallback)(callback => {
    let timeoutId;
    return (...args) => {
      clearTimeout(timeoutId);
      timeoutId = setTimeout(() => callback(...args), 300);
    };
  }, []);
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.ComboboxControl, {
    value: value,
    options: options,
    isLoading: isLoading,
    onChange: onInputChange,
    placeholder: (0,external_this_wp_i18n_.__)('Search for a post', 'polylang-pro'),
    onFilterValueChange: debounce(fetchUntranslatedPosts),
    __next40pxDefaultSize: true
  });
};

/**
 * Gets the post title.
 *
 * @param {Object} post The post object.
 * @return {string} The post title, or an empty string if the post has no title.
 */
const getPostTitle = post => {
  /**
   * @member {{rendered: ?string, raw: ?string}|string} post.title
   */
  return (0,external_this_wp_htmlEntities_.decodeEntities)(post.title?.rendered ?? post.title?.raw ?? post.title ?? '');
};

/**
 * Gets the post to render.
 *
 * @param {Object} options   The options object.
 * @param {string} nextValue The next value.
 * @return {Object|null} The post to render, or null if the post is not found.
 */
const getPostToRender = (options, nextValue) => {
  const value = options.find(option => option.value === nextValue);
  if (!value) {
    return null;
  }
  return {
    id: value.value,
    title: value.label
  };
};

/**
 * Transforms the translations table to an object.
 *
 * @param {Map<Object, Object>} translationsTable The translations table.
 * @return {Object<string, number>} The translations object.
 */
const transformTranslationsTableToObject = translationsTable => {
  const translationsObject = {};
  translationsTable.forEach((translationPost, lang) => {
    if (!translationPost || !translationPost.id) {
      return;
    }
    translationsObject[lang.slug] = translationPost.id;
  });
  return translationsObject;
};
/* harmony default export */ const input = (TranslationInput);
;// ./js/src/editors/common/components/translations-table/rows/index.js
/**
 * Internal dependencies.
 */


const TranslationRow = ({
  language,
  children
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)("tr", {
    children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(flag, {
      language: language
    }), children]
  });
};
/* harmony default export */ const rows = (TranslationRow);
;// ./js/src/editors/common/hooks/use-can-create-permission.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Custom hook to control the create permission in authorized languages.
 *
 * @param {Object} language    The language object.
 * @param {Object} currentPost The current post object.
 *
 * @return {Boolean}
 */
const useCanCreatePermission = (language, currentPost) => {
  const authorizedLanguages = use_authorized_languages();
  return (0,external_this_wp_data_.useSelect)(select => authorizedLanguages.has(language.slug) ? select(external_this_wp_coreData_.store).canUser('create', {
    kind: 'postType',
    name: currentPost.type
  }) : false, [authorizedLanguages, language.slug, currentPost.type]);
};
/* harmony default export */ const use_can_create_permission = (useCanCreatePermission);
;// ./js/src/editors/common/hooks/use-can-update-permission.js
/**
 * WordPress dependencies
 */



/**
 * Custom hook to control the update permission
 *
 * @param {Object} post The post object
 *
 * @return {Boolean}
 */
const useCanUpdatePermission = post => {
  return (0,external_this_wp_data_.useSelect)(select => select(external_this_wp_coreData_.store).canUser('update', {
    kind: 'postType',
    name: post.type,
    id: post.id
  }), [post.type, post.id]);
};
/* harmony default export */ const use_can_update_permission = (useCanUpdatePermission);
;// ./js/src/editors/common/components/translations-table/post-editor/add-or-edit-container.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




/**
 * Add button container.
 *
 * @param {Object} props             The component props.
 * @param {Object} props.language    The language object.
 * @param {Object} props.currentPost The current post object.
 * @return {React.Component}         The Add button component.
 */

const AddContainer = ({
  language,
  currentPost
}) => {
  const canCreatePermission = use_can_create_permission(language, currentPost);
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(add, {
    href: pll_block_editor_plugin_settings.new_post_translation_links[language.slug],
    language: language,
    disabled: !canCreatePermission
  });
};

/**
 * Edit button container.
 *
 * @param {Object} props             The component props.
 * @param {Object} props.language    The language object.
 * @param {Object} props.translation The translation post object.
 * @param {Object} props.currentPost The current post object.
 * @return {React.Component}         The Edit button component.
 */
const EditContainer = ({
  language,
  translation,
  currentPost
}) => {
  const canUpdatePermission = use_can_update_permission(translation);
  const translationId = currentPost.translations[language.slug] ?? null;
  const baseEditUrl = `${location.origin}/${(0,external_this_wp_url_.getPath)(location.href)}`.replace('post-new.php', 'post.php');
  const editUrl = `${baseEditUrl}?post=${translationId}&action=edit`;

  // Show Edit button for existing translations (disabled if user can only read).
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(edit, {
    href: editUrl,
    language: language,
    disabled: !canUpdatePermission
  });
};

/**
 * Add or edit button container, used in post editor because of clickable links.
 *
 * @param {Object}   	props             The component props.
 * @param {Object}   	props.language    The language object.
 * @param {Object|null} props.translation The translation post object.
 * @param {Object}   	props.currentPost The current post object.
 * @return {React.Component}           	  The Add or Edit button component.
 */
const AddOrEditContainer = ({
  language,
  translation,
  currentPost
}) => {
  if (translation) {
    return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(EditContainer, {
      language: language,
      translation: translation,
      currentPost: currentPost
    });
  }
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(AddContainer, {
    language: language,
    currentPost: currentPost
  });
};
/* harmony default export */ const add_or_edit_container = (AddOrEditContainer);
;// ./js/src/editors/common/components/translations-table/rows/post-editor.js
/**
 * Internal dependencies
 */






/**
 * Post Editor Translation Row component.
 *
 * @param {Object}   props                   The component props.
 * @param {Object}   props.language          The language object.
 * @param {Object}   props.translation       The translation post object.
 * @param {Object}   props.currentPost       The current post.
 * @param {Map}      props.translationsTable The translations table.
 * @param {Function} props.tableDispatch     The translations table dispatch function.
 * @return {React.Component} The Post Editor Translation Row component.
 */

const PostEditorTranslationRow = ({
  language,
  translation,
  currentPost,
  translationsTable,
  tableDispatch
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)(rows, {
    language: language,
    children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(add_or_edit, {
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(add_or_edit_container, {
        language: language,
        translation: translation,
        currentPost: currentPost
      })
    }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(PluginFeatureTableRowButtonSlot, {
      language: language,
      translation: translation,
      translationTableReducer: tableDispatch
    }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(translation_input, {
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(input, {
        language: language,
        source: currentPost,
        translation: translation,
        tableDispatch: tableDispatch,
        translationsTable: translationsTable
      })
    })]
  });
};
/* harmony default export */ const post_editor = (PostEditorTranslationRow);
;// ./js/src/editors/common/components/translations-table/wrapper/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


const TranslationsTableWrapper = ({
  children
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)("div", {
    id: "post-translations",
    className: "translations",
    children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)("div", {
      className: "pll-translations-table-header",
      children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("p", {
        className: "pll-translations-table-header-title",
        children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("strong", {
          children: (0,external_this_wp_i18n_.__)('Translations', 'polylang-pro')
        })
      }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(plugin_feature_flag_button.Slot, {
        children: fills => fills.length > 0 && /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)((react_jsx_runtime_production_min_namespaceFn().Fragment), {
          children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("div", {
            className: "pll-plugin-feature-button-container",
            children: fills
          })
        })
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("table", {
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("tbody", {
        children: children
      })
    })]
  });
};
/* harmony default export */ const wrapper = (TranslationsTableWrapper);
;// ./js/src/editors/common/components/translations-table/post-editor/index.js
/**
 * Internal dependencies
 */




/**
 * Post Editor Translations Table component.
 *
 * @param {Object}   props                   The component props.
 * @param {Object}   props.currentPost       The current post.
 * @param {Map}      props.translationsTable The translations table, contains language object as key and post object as value.
 * @param {Function} props.tableDispatch     The translations table dispatch function.
 * @return {React.Component} The Post Editor Translations Table component.
 */

const PostEditorTranslationsTable = ({
  currentPost,
  translationsTable,
  tableDispatch
}) => {
  const {
    language: currentLanguage
  } = useCurrentLanguage();
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(wrapper, {
    children: Array.from(translationsTable).map(([language, translation]) => {
      if (currentLanguage?.slug === language.slug) {
        return null;
      }
      return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(post_editor, {
        language: language,
        translation: translation,
        currentPost: currentPost,
        translationsTable: translationsTable,
        tableDispatch: tableDispatch
      }, language.slug);
    })
  });
};
/* harmony default export */ const translations_table_post_editor = (PostEditorTranslationsTable);
;// ./js/src/editors/common/components/not-translatable-notice/index.js
/**
 * WordPress Dependencies.
 */


const NotTranslatableNotice = ({
  postType
}) => {
  if ('wp_template' === postType) {
    return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("div", {
      className: "pll-metabox-error components-notice is-warning",
      children: (0,external_this_wp_i18n_.__)('Templates are not translatable, only template parts are.', 'polylang-pro')
    });
  }
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("div", {
    className: "pll-metabox-error components-notice is-warning",
    children: (0,external_this_wp_i18n_.__)('This entity is not translatable.', 'polylang-pro')
  });
};
/* harmony default export */ const not_translatable_notice = (NotTranslatableNotice);
;// ./js/src/editors/common/components/metaboxes/wrapper/index.js

const MetaboxWrapper = ({
  children
}) => {
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("div", {
    className: "components-panel__body is-opened",
    children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("div", {
      className: "pll-metabox-location",
      children: children
    })
  });
};
/* harmony default export */ const metaboxes_wrapper = (MetaboxWrapper);
;// ./js/src/editors/common/components/metaboxes/container/index.js
/**
 * WordPress Dependencies.
 */


/**
 * Internal Dependencies.
 */



const MetaboxContainer = ({
  isError,
  isAllowedPostType,
  postType,
  children
}) => {
  if (!isAllowedPostType) {
    return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(metaboxes_wrapper, {
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(not_translatable_notice, {
        postType: postType
      })
    });
  }
  if (isError) {
    return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(metaboxes_wrapper, {
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("div", {
        className: "pll-metabox-error components-notice is-error",
        children: (0,external_this_wp_i18n_.__)('Unable to retrieve the content language', 'polylang-pro')
      })
    });
  }
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(metaboxes_wrapper, {
    children: children
  });
};
/* harmony default export */ const container = (MetaboxContainer);
;// ./js/src/editors/common/components/metaboxes/translations-table-reducer/index.js
/**
 * Reducer for the translations table.
 *
 * @param {Map}    state  The current state.
 * @param {Object} action The action to perform.
 * @return {Map} The new state.
 */
const translationsTableReducer = (state, action) => {
  switch (action.type) {
    case 'add_translation':
      state.set(action.lang, action.post);
      return new Map(state);
    case 'remove_translation':
      state.set(action.lang, null);
      return new Map(state);
    case 'set_table':
      return normalizeTranslations(action.languages, action.translations);
    default:
      throw new Error('Invalid action');
  }
};

/**
 * Normalizes the translations table.
 *
 * @param {Array}  languages    The list of language objects.
 * @param {Object} translations The translations IDs keyed by language slug.
 * @return {Map} The normalized translations table.
 */
const normalizeTranslations = (languages, translations) => {
  const translationsTable = new Map();
  languages.forEach(language => {
    translationsTable.set(language, translations[language.slug] ?? null);
  });
  return translationsTable;
};
;// ./js/src/editors/common/components/metaboxes/post-editor/index.js
/**
 * WordPress Dependencies.
 */




/**
 * Internal Dependencies.
 */







const PostEditorMetabox = () => {
  const [translationsTable, tableDispatch] = (0,external_this_wp_element_.useReducer)(translationsTableReducer, new Map());

  // Hook before empty-table return; currentLanguage is only read when the table renders.
  // eslint-disable-next-line @wordpress/no-unused-vars-before-return
  const {
    language: currentLanguage
  } = useCurrentLanguage();
  const languages = (0,external_this_wp_data_.useSelect)(select => select(MODULE_KEY).getLanguages());
  const {
    currentPost,
    isAllowedPostType
  } = (0,external_this_wp_data_.useSelect)(select => {
    const post = select(MODULE_CORE_EDITOR_KEY).getCurrentPost();
    const isAllowed = !UNTRANSLATABLE_POST_TYPE.includes(post?.type);
    return {
      currentPost: post,
      isAllowedPostType: isAllowed
    };
  }, []);
  const translationsData = (0,external_this_wp_data_.useSelect)(select => {
    if (!currentPost || !currentPost.translations) {
      return {};
    }
    let translations = currentPost.translations;
    if ('auto-draft' === currentPost.status) {
      const fromPostUrlParams = select(MODULE_KEY).getFromPost();
      if (fromPostUrlParams) {
        const fromPostData = select(external_this_wp_coreData_.store).getEntityRecord('postType', currentPost.type, fromPostUrlParams.id, {
          context: 'view'
        } // Use 'view' context so translators can read posts they cannot edit.
        );
        if (fromPostData) {
          translations = {
            ...fromPostData.translations,
            [currentPost.lang]: currentPost.id
          };
        }
      }
    }
    const results = {};
    const translatedPosts = select(external_this_wp_coreData_.store).getEntityRecords('postType', currentPost.type, {
      include: Object.values(translations),
      context: 'view',
      status: 'any',
      per_page: Object.values(translations).length,
      lang: '' // Disables language filter middleware.
    });
    translatedPosts?.forEach(post => {
      const translationData = getTranslationData(post.lang, translationsTable);

      /*
       * Preserve the optimistic placeholder (id: 0) until the server returns the real translation ID,
       * then let the real entity record be fetched.
       */
      if (translationData && translationData.id === post.id) {
        results[post.lang] = translationData;
        return;
      }
      results[post.lang] = post;
    });
    return results;
  }, [currentPost, translationsTable]);
  (0,external_this_wp_element_.useEffect)(() => {
    tableDispatch({
      type: 'set_table',
      languages,
      translations: translationsData
    });
  }, [translationsData, languages, tableDispatch]);
  if (translationsTable.size === 0) {
    return null;
  }
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)(container, {
    isError: !currentLanguage,
    isAllowedPostType: isAllowedPostType,
    postType: currentPost?.type,
    children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(switcher, {
      currentPost: currentPost
    }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(translations_table_post_editor, {
      currentPost: currentPost,
      translationsTable: translationsTable,
      tableDispatch: tableDispatch
    })]
  });
};

/**
 * Gets data from the translations table map for a given language.
 *
 * @param {string} langSlug          The language slug.
 * @param {Map}    translationsTable The translations table.
 * @return {Object|null} The translation data or null if not found.
 */
const getTranslationData = (langSlug, translationsTable) => {
  for (const [lang, post] of translationsTable) {
    if (lang.slug === langSlug) {
      return post;
    }
  }
  return null;
};
/* harmony default export */ const metaboxes_post_editor = (PostEditorMetabox);
;// ./js/src/editors/common/components/sidebar/index.js
/**
 * WordPress dependencies
 */


const Sidebar = ({
  SidebarSlot,
  MoreMenuItemSlot,
  sidebarName,
  children
}) => {
  const title = (0,external_this_wp_i18n_.__)('Languages', 'polylang-pro');
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)((react_jsx_runtime_production_min_namespaceFn().Fragment), {
    children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(SidebarSlot, {
      name: sidebarName,
      title: title,
      children: children
    }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(MoreMenuItemSlot, {
      target: sidebarName,
      children: title
    })]
  });
};
/* harmony default export */ const sidebar = (Sidebar);
;// ./js/src/editors/common/store/index.js
/**
 * WordPress Dependencies
 */



/**
 * Internal dependencies
 */


const actions = {
  setLanguages(languages) {
    return {
      type: 'SET_LANGUAGES',
      languages
    };
  },
  setFromPost(fromPost) {
    return {
      type: 'SET_FROM_POST',
      fromPost
    };
  },
  fetchFromAPI(options) {
    return {
      type: 'FETCH_FROM_API',
      options
    };
  }
};
const store = (0,external_this_wp_data_.createReduxStore)(MODULE_KEY, {
  reducer(state = DEFAULT_STATE, action) {
    switch (action.type) {
      case 'SET_LANGUAGES':
        return {
          ...state,
          languages: action.languages
        };
      case 'SET_FROM_POST':
        return {
          ...state,
          fromPost: action.fromPost
        };
      case 'SET_CURRENT_TEMPLATE_PART':
        return {
          ...state,
          currentTemplatePart: action.currentTemplatePart
        };
      default:
        return state;
    }
  },
  selectors: {
    getLanguages(state) {
      return state.languages;
    },
    getFromPost(state) {
      return state.fromPost;
    }
  },
  actions,
  controls: {
    FETCH_FROM_API(action) {
      return external_this_wp_apiFetch_default()({
        ...action.options
      });
    }
  },
  resolvers: {
    *getLanguages() {
      const path = '/pll/v1/languages';
      const languages = yield actions.fetchFromAPI({
        path,
        filterLang: false
      });
      return actions.setLanguages(convertArrayToMap(languages, 'slug'));
    }
  }
});
(0,external_this_wp_data_.register)(store);
;// ./js/src/editors/post/index.js
/**
 * Import styles
 */


/**
 * WordPress dependencies
 */



/**
 * External dependencies
 */


/**
 * Internal dependencies
 */










editors_requests_filter(addParametersToRequest);
seedCurrentLanguageFromSettings();
const sidebarName = 'polylang-sidebar';
const settingsErrors = pll_block_editor_plugin_settings?.machine_translation?.errors;
const _root = document.createElement('div');
_root.id = 'pll-root';
const post_root = document.body.appendChild(_root);
(0,external_this_wp_element_.createRoot)(post_root).render(/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)((react_jsx_runtime_production_min_namespaceFn().Fragment), {
  children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(rest_bridge, {}), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(app, {
    sidebar: () => {
      return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(sidebar, {
        SidebarSlot: external_this_wp_editor_.PluginSidebar,
        MoreMenuItemSlot: external_this_wp_editor_.PluginSidebarMoreMenuItem,
        sidebarName: sidebarName,
        children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(metaboxes_post_editor, {})
      });
    },
    sidebarName: sidebarName,
    onPromise: isBlockPostEditorContextInitialized,
    children: undefined !== settingsErrors && /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(display_notices, {
      notices: settingsErrors
    })
  })]
}));
isBlockPostEditorContextInitialized().then(() => {
  registerProFeatures();
});
})();

this["polylang-pro"] = __webpack_exports__;
/******/ })()
;