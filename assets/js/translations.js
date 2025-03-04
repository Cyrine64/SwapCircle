// Import the Translator
import Translator from 'bazinga-translator';

// Initialize the translator
const translator = Translator.fromGlobal();

export function translate(key, parameters = {}, domain = 'comments') {
    return translator.trans(key, parameters, domain);
}

// Example usage:
// translate('comment.add')
// translate('comment.count', { count: 5 })
