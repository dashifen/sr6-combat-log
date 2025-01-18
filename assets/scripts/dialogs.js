export const Dialogs = {
  /**
   * Attaches event listeners to the necessary parts of our dialogs.
   *
   * @param {HTMLDialogElement} dialog
   */
  initializeDialog(dialog) {
    
    // we've arranged our dialogs such that there are elements with the
    // .dialog-cancel and .dialog-proceed classes.  we'll want to grab those
    // and attach other methods of this object to them now.
    
    this.initializeCancel(dialog);
    this.initializeProceed(dialog);
  },
  
  /**
   * Finds .dialog-cancel elements and makes sure they close the dialog while
   * indicating that the application should not do anything when it does.
   *
   * @param {HTMLDialogElement} dialog
   *
   * @return {void}
   */
  initializeCancel(dialog) {
    dialog.querySelectorAll('.dialog-cancel').forEach(element => {
      element.addEventListener('click', (event) => {
        event.preventDefault();
        dialog.close();
      });
    });
  },
  
  /**
   * Finds .dialog-proceed elements and makes sure they close the dialog while
   * indicating that the application should do something when it does.
   *
   * @param {HTMLDialogElement} dialog
   *
   * @return {void}
   */
  initializeProceed(dialog) {
    dialog.querySelectorAll('.dialog-proceed').forEach(element => {
      element.addEventListener('click', (event) => {
        event.preventDefault();
        
        // unlike the cancel action above, the proceed action has a little bit
        // more to do:  check for a field with a .return-value class.  if we
        // find one, then we use its value as the dialog's return value.
        // otherwise, we just close the dialog and let the default behaviors
        // take over.
        
        const returnField = dialog.querySelector('.return-value');
        !!returnField ? dialog.close(returnField.value) : dialog.close();
      })
    });
  },
  
  /**
   * Prepares an interval that watches the given dialog's closure.
   *
   * @param {HTMLDialogElement} dialog
   * @param {Function} callback
   */
  watch(dialog, callback) {
    const interval = setInterval(() => {
      if (!dialog.open) {
        
        // when the interval notices that the dialog has closed, it'll check
        // its return value.  if it's something other than cancel, then we
        // execute the callback (since not canceling something is to proceed).
        // regardless of what's next, though, we clear the interval and end our
        // watch.
        
        if (dialog.returnValue !== 'cancel') {
          callback(dialog.returnValue);
        }
        
        clearInterval(interval);
      }
    }, 500);
  }
};
