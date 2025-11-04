/**
 * @file defines InsertBootstrapIconsCommand, which is executed when the icon
 * toolbar button is pressed.
 */
// cSpell:ignore flagIcons

import { Command } from 'ckeditor5/src/core';

export default class InsertIconFlagCommand extends Command {
  execute(addClass) {
    const { model } = this.editor;
    const config = this.editor.config.get('flag_icons');
    let fi = addClass.icon.split(' | ');
    model.change((writer) => {
      let classes = config.img ? '' : 'fi ';
      let classFi = fi[0];
      let tooltip = fi.pop();
      if (addClass.icon !== '') {
        classes += 'fi-' + classFi;
      }
      if (config.ratio == '1x1') {
        classes += ' fis';
      }
      const attributes = {
        class: classes,
      };
      let img = `<img class="flag-img" height="25" src="${config.url}${classFi}.svg" alt="${tooltip}"/>`;
      const content = writer.createElement('flagIcons', attributes);
      const docFrag = writer.createDocumentFragment();
      if(config.img) {
        const viewFragment = this.editor.data.processor.toView(img);
        const modelFragment = this.editor.data.toModel(viewFragment);
        writer.append(modelFragment, content);
      }
      writer.append(content, docFrag);
      this.editor.model.insertContent(docFrag);
    });
  }

  refresh() {
    const { model } = this.editor;
    const { selection } = model.document;
    const allowedIn = model.schema.findAllowedParent(
      selection.getFirstPosition(),
      'flagIcons',
    );
    this.isEnabled = allowedIn !== null;
  }
}
