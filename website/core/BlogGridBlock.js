/**
 * Copyright (c) 2017-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */
const React = require('react');
const classNames = require('classnames');
const CompLibrary = require(process.cwd()+'/node_modules/docusaurus/lib/core/CompLibrary.js');
const MetadataBlog = require(process.cwd()+'/node_modules/docusaurus/lib/core/MetadataBlog.js');
const utils = require(process.cwd()+'/node_modules/docusaurus/lib/core/utils.js');
const siteConfig = require(process.cwd() + '/siteConfig.js');
const MarkdownBlock = CompLibrary.MarkdownBlock;
const GridBlock = CompLibrary.GridBlock;




class BlogGridBlock extends GridBlock {
  constructor(){
    super();
  }

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
        {topLeftImage}
        <div className="blockContent">
          {this.renderBlockTitleAndDate(block.title, block.date)}
          <MarkdownBlock>{utils.extractBlogPostBeforeTruncate(block.content)}</MarkdownBlock>
        </div>
        <div className="pluginWrapper buttonWrapper">
          <a className="button" href={siteConfig.baseUrl +'blog/' +utils.getPath(block.path, siteConfig.cleanUrl)}>
            Leia mais
          </a>
        </div>
      </div>
    );
  }

  render() {
    this.loadPosts();
    return (
      <div className="gridBlock">
        {this.props.contents.map(this.renderBlock, this)}
      </div>
    );
  }

  renderBlockTitleAndDate(title, date) {
    if (title) {
      return (
        <div>
          <h2>
            <MarkdownBlock>{title}</MarkdownBlock>
          </h2>
          <p className="blogdate">
            {this.formatDate(date)}
          </p>
        </div>
      );
    } else {
      return null;
    }
  }

  formatDate (date) {
    return date.getDate() + '/' + (date.getMonth()+1) + '/' + date.getFullYear();
  }

  loadPosts () {
    let lastThreePosts = MetadataBlog.slice(0,3);
    lastThreePosts.map(function(post) {
      this.props.contents.push ({title: post.title, content: post.content, date: new Date(post.seconds*1000), path: post.path});
    }, this);
  }
}

BlogGridBlock.defaultProps = {
  align: 'left',
  contents: [],
  layout: 'twoColumn',
};
module.exports = BlogGridBlock;
