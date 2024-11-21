export function getRelativeRootPath(rootIdentifier = '') {
    // Get the current module's full URL
    const moduleUrl = new URL(import.meta.url);

    // Extract the pathname (file system-like path)
    const modulePath = moduleUrl.pathname;

    // Split the path into parts
    const pathParts = modulePath.split('/');

    // Find the position of the root identifier in the path
    const rootIndex = rootIdentifier
        ? pathParts.findIndex((part) => part === rootIdentifier)
        : 1; // Default to the first directory if no identifier is provided

    if (rootIndex === -1) {
        throw new Error(
            `Root identifier "${rootIdentifier}" not found in the module path: ${modulePath}`
        );
    }

    // Calculate the number of directories to go up
    const levelsUp = pathParts.length - rootIndex - 2;

    // Build the relative path
    const relativePath = '../'.repeat(levelsUp);

    return relativePath || './'; // Default to './' if already at root
}