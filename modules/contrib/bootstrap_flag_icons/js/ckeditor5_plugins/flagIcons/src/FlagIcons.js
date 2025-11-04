/**
 * @file The build process always expects an index.js file. Anything exported
 * here will be recognized by CKEditor 5 as an available plugin. Multiple
 * plugins can be exported in this one file.
 *
 * I.e. this file's purpose is to make plugin(s) discoverable.
 */
// cSpell:ignore flagicons

import { Plugin } from 'ckeditor5/src/core';
import FlagIconsUI from "./FlagIconsUI";
import FlagIconsEditing from "./FlagIconsEditing";

export default class FlagIcons extends Plugin {
  /**
   * @inheritdoc
   */
  static get requires() {
    return [FlagIconsEditing, FlagIconsUI];
  }

  /**
   * @inheritdoc
   */
  static get pluginName() {
    return 'FlagIcons';
  }
}
