import { Plugin } from 'ckeditor5/src/core';
import { Widget } from 'ckeditor5/src/widget';
import InsertIconFlagCommand from "./InsertIconFlagCommand";

// cSpell:ignore flagicons inserticoncommand

/**
 * CKEditor 5 plugins do not work directly with the DOM. They are defined as
 * plugin-specific data models that are then converted to markup that
 * is inserted in the DOM.
 *
 * CKEditor 5 internally interacts with simpleBox as this model:
 * <simpleBox>
 *    <simpleBoxTitle></simpleBoxTitle>
 *    <simpleBoxDescription></simpleBoxDescription>
 * </simpleBox>
 *
 * Which is converted for the browser/user as this markup
 * <section class="simple-box">
 *   <h2 class="simple-box-title"></h1>
 *   <div class="simple-box-description"></div>
 * </section>
 *
 * This file has the logic for defining the simpleBox model, and for how it is
 * converted to standard DOM markup.
 */
export default class FlagIconsEditing extends Plugin {
  static get requires() {
    return [Widget];
  }

  init() {
    this._defineSchema();
    this._defineConverters();
    this._defineCommands();
  }

  _defineSchema() {
    // Schemas are registered via the central `editor` object.
    const schema = this.editor.model.schema;

    schema.register('flagIcons', {
      // Behaves like a self-contained object (e.g. an image).
      isObject: true,
      // Allow in places where other blocks are allowed (e.g. directly in the root).
      allowWhere: '$text',
      isInline: true,
      allowAttributes: ['class'],
    });
  }

  /**
   * Converters determine how CKEditor 5 models are converted into markup and
   * vice-versa.
   */
  _defineConverters() {
    // Converters are registered via the central editor object.
    const { conversion } = this.editor;
    // Allow class attribute.
    conversion.attributeToAttribute({model: 'class', view: 'class'});
    conversion.for('downcast').elementToElement({
      model: 'flagIcons',
      view: 'i'
    });

    conversion.for('upcast').elementToElement({
      model: 'flagIcons',
      view: {
        name: 'i',
        classes: 'flag-icons',
      },
    });
  }

  _defineCommands() {
    const editor = this.editor;
    editor.commands.add(
      'InsertIconFlagCommand',
      new InsertIconFlagCommand(this.editor),
    );
  }
}
