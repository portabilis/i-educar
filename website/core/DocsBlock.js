/**
 * Copyright (c) 2017-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */
const React = require('react');
const classNames = require('classnames');
const CompLibrary = require(process.cwd()+'/node_modules/docusaurus/lib/core/CompLibrary.js');
const utils = require(process.cwd()+'/node_modules/docusaurus/lib/core/utils.js');
const siteConfig = require(process.cwd() + '/siteConfig.js');
const MarkdownBlock = CompLibrary.MarkdownBlock;
const GridBlock = CompLibrary.GridBlock;




class DocsBlock extends GridBlock {

  renderBlock(block) {
    const blockClasses = classNames('blockElement', this.props.className, {
      alignCenter: this.props.align === 'center',
      alignRight: this.props.align === 'right',
      fourByGridBlock: this.props.layout === 'fourColumn',
      imageAlignSide:
        block.image &&
        (block.imageAlign === 'left' || block.imageAlign === 'right'),
      imageAlignTop: block.image && block.imageAlign === 'top',
      imageAlignRight: block.image && block.imageAlign === 'right',
      imageAlignBottom: block.image && block.imageAlign === 'bottom',
      imageAlignLeft: block.image && block.imageAlign === 'left',
      threeByGridBlock: this.props.layout === 'threeColumn',
      twoByGridBlock: this.props.layout === 'twoColumn',
    });

    const topLeftImage =
      (block.imageAlign === 'top' || block.imageAlign === 'left') &&
      this.renderBlockImage(block.image, block.imageLink, block.imageAlt);

    const bottomRightImage =
      (block.imageAlign === 'bottom' || block.imageAlign === 'right') &&
      this.renderBlockImage(block.image, block.imageLink, block.imageAlt);

    return (
      <div className={blockClasses} key={block.title}>
        <div className="docsGridTitle">
          {topLeftImage} {this.renderBlockTitle(block.title)}
        </div>
        <div className="blockContent">
          <MarkdownBlock>{utils.extractBlogPostBeforeTruncate(block.content)}</MarkdownBlock>
          <a className="docsLink" href={block.link}>
            Visitar guia completo
          </a>
        </div>

      </div>
    );
  }

  render() {
    return (
      <div className="gridBlock">
        {this.props.contents.map(this.renderBlock, this)}
      </div>
    );
  }

  renderBlockTitle(title) {
    if (title) {
      return (
        <div>
          <h2>
            <MarkdownBlock>{title}</MarkdownBlock>
          </h2>
        </div>
      );
    } else {
      return null;
    }
  }
}

module.exports = DocsBlock;
