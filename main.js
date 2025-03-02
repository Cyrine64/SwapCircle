const fs = require('fs');

// Read the file
const processFile = (filePath) => {
    try {
        let content = fs.readFileSync(filePath, 'utf8');
        
        // Regular expression to match img src attributes
        const imgRegex = /src=["'](.*?)["']/g;
        
        // Replace all image sources
        content = content.replace(imgRegex, (match, src) => {
            // Skip if already using asset
            if (src.includes('{{')) {
                return match;
            }
            
            // Extract filename
            const filename = src.split('/').pop();
            
            // Create new asset path
            return `src="{{ asset('backOffice/images/${filename}') }}"`;
        });
        
        // Write the modified content back to file
        fs.writeFileSync(filePath, content);
        console.log('File processed successfully!');
        
    } catch (error) {
        console.error('Error processing file:', error);
    }
};

// Usage example:
processFile('./templates/back-base.html.twig');