<template>
  <div>
    <div v-if="editor">
      <q-btn type="button" @click="editor.chain().focus().toggleBold().run()" :class="{ 'is-active': editor.isActive('bold') }">
        bold
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().toggleItalic().run()" :class="{ 'is-active': editor.isActive('italic') }">
        italic
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().toggleStrike().run()" :class="{ 'is-active': editor.isActive('strike') }">
        strike
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().toggleCode().run()" :class="{ 'is-active': editor.isActive('code') }">
        code
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().unsetAllMarks().run()">
        clear marks
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().clearNodes().run()">
        clear nodes
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().setParagraph().run()" :class="{ 'is-active': editor.isActive('paragraph') }">
        paragraph
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().toggleHeading({ level: 1 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 1 }) }">
        h1
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 2 }) }">
        h2
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 3 }) }">
        h3
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().toggleHeading({ level: 4 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 4 }) }">
        h4
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().toggleHeading({ level: 5 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 5 }) }">
        h5
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().toggleHeading({ level: 6 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 6 }) }">
        h6
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().toggleBulletList().run()" :class="{ 'is-active': editor.isActive('bulletList') }">
        bullet list
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().toggleOrderedList().run()" :class="{ 'is-active': editor.isActive('orderedList') }">
        ordered list
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().toggleCodeBlock().run()" :class="{ 'is-active': editor.isActive('codeBlock') }">
        code block
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().toggleBlockquote().run()" :class="{ 'is-active': editor.isActive('blockquote') }">
        blockquote
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().setHorizontalRule().run()">
        horizontal rule
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().setHardBreak().run()">
        hard break
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().undo().run()">
        undo
      </q-btn>
      <q-btn type="button" @click="editor.chain().focus().redo().run()">
        redo
      </q-btn>
    </div>
    <editor-content :editor="editor" />
  </div>
</template>

<script>
import { Editor, EditorContent } from '@tiptap/vue-2'
import StarterKit from '@tiptap/starter-kit'
import Document from '@tiptap/extension-document'
import Paragraph from '@tiptap/extension-paragraph'
import Text from '@tiptap/extension-text'
import TaskList from '@tiptap/extension-task-list'
import TaskItem from '@tiptap/extension-task-item'

// import CodeBlockLowlight from '@tiptap/extension-code-block-lowlight'
// import CodeBlock from './CodeBlock'

// load all highlight.js languages
// import lowlight from 'lowlight'

const CustomDocument = Document.extend({
  // content: 'taskList'
})

const CustomTaskItem = TaskItem.extend({
  // content: 'inline*'
})

export default {
  name: 'Editor',
  components: {
    EditorContent
  },

  props: {
    value: {
      type: String,
      default: ''
    }
  },

  data () {
    return {
      editor: null
    }
  },

  watch: {
    value (value) {
      // HTML
      const isSame = this.editor.getHTML() === value

      // JSON
      // const isSame = this.editor.getJSON().toString() === value.toString()

      if (isSame) {
        return
      }

      this.editor.commands.setContent(this.value, false)
    }
  },

  mounted () {
    this.editor = new Editor({
      extensions: [
        StarterKit,
        CustomDocument,
        Paragraph,
        Text,
        TaskList,
        CustomTaskItem
        // CodeBlockLowlight.extend({
        //   addNodeView () {
        //     return VueNodeViewRenderer(CodeBlock)
        //   }
        // }).configure({ lowlight })
      ],
      content: this.value,
      onUpdate: () => {
        // HTML
        this.$emit('input', this.editor.getHTML())

        // JSON
        // this.$emit('input', this.editor.getJSON())
      }
    })
  },

  beforeDestroy () {
    this.editor.destroy()
  }
}
</script>

<style lang="scss">
/* Basic editor styles */
.ProseMirror {
  > * + * {
    margin-top: 0.75em;
  }

  ul,
  ol {
    padding: 0 1rem;
  }

  h1,
  h2,
  h3,
  h4,
  h5,
  h6 {
    line-height: 1.1;
  }

  code {
    background-color: rgba(#616161, 0.1);
    color: #616161;
  }

  pre {
    background: #0D0D0D;
    color: #FFF;
    font-family: 'JetBrainsMono', monospace;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;

    code {
      color: inherit;
      padding: 0;
      background: none;
      font-size: 0.8rem;
    }

    .hljs-comment,
    .hljs-quote {
      color: #616161;
    }

    .hljs-variable,
    .hljs-template-variable,
    .hljs-attribute,
    .hljs-tag,
    .hljs-name,
    .hljs-regexp,
    .hljs-link,
    .hljs-name,
    .hljs-selector-id,
    .hljs-selector-class {
      color: #F98181;
    }

    .hljs-number,
    .hljs-meta,
    .hljs-built_in,
    .hljs-builtin-name,
    .hljs-literal,
    .hljs-type,
    .hljs-params {
      color: #FBBC88;
    }

    .hljs-string,
    .hljs-symbol,
    .hljs-bullet {
      color: #B9F18D;
    }

    .hljs-title,
    .hljs-section {
      color: #FAF594;
    }

    .hljs-keyword,
    .hljs-selector-tag {
      color: #70CFF8;
    }

    .hljs-emphasis {
      font-style: italic;
    }

    .hljs-strong {
      font-weight: 700;
    }
  }

  img {
    max-width: 100%;
    height: auto;
  }

  blockquote {
    padding-left: 1rem;
    border-left: 2px solid rgba(#0D0D0D, 0.1);
  }

  hr {
    border: none;
    border-top: 2px solid rgba(#0D0D0D, 0.1);
    margin: 2rem 0;
  }

  // tasklist
  ul[data-type="taskList"] {
    list-style: none;
    padding: 0;

    li {
      display: flex;
      align-items: center;

      > label {
        flex: 0 0 auto;
        margin-right: 0.5rem;
      }
    }

    input[type="checkbox"] {
      cursor: pointer;
    }
  }
}
</style>
